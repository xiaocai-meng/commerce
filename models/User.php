<?php
namespace app\models;
use yii\db\ActiveRecord;
use Yii;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public $confirmPassword = FALSE;
    public $rememberMe = TRUE;
    public $loginname;

    public static function tableName() {
        return "{{%user}}";
    }

    public function attributeLabels() {
        return [
            'username' => '用户名',
            'useremail' => '用户邮箱',
            'userpassword' => '用户密码',
            'confirmPassword' => '确认密码',
            'loginname' => '用户名或者邮箱',
        ];
    }

    public function rules() {
        return [
            ['loginname', 'required', 'message' => '用户名或者邮箱不能为空', 'on' => 'login'],
            ['openid', 'required', 'message' => 'openid不能为空', 'on' => 'qqreg'],
            ['username', 'required', 'message' => '用户账号不能为空', 'on' => ['adduser', 'reg', 'qqreg']],
            ['openid', 'unique', 'message' => 'openid已经被注册', 'on' => 'qqreg'],
            ['username', 'unique', 'message' => '已经被注册', 'on' => ['adduser', 'reg', 'qqreg']],
            ['userpassword', 'required', 'message' => '用户密码不能为空', 'on' => ['adduser', 'reg', 'login', 'qqreg']],
            ['userpassword', 'validatePassword', 'on' => 'login'],
            ['useremail', 'required', 'message' => '用户邮箱不能为空', 'on' => ['adduser', 'reg']],
            ['useremail', 'unique', 'message' => '用户邮箱已经被注册', 'on' => ['adduser', 'reg']],
            ['useremail', 'email', 'message' => '用户邮箱格式不正确', 'on' => ['adduser', 'reg']],
            ['confirmPassword', 'required', 'message' => '确认密码不能为空', 'on' => ['adduser', 'qqreg']],
            ['confirmPassword', 'compare', 'compareAttribute' => 'userpassword', 'message' => '两次密码不一致', 'on' => ['adduser', 'qqreg']],
            ['rememberMe', 'safe']
        ];
    }

    public function validatePassword() {
        //如果前面验证没有错误在进行对数据库的查询
        if (!$this->hasErrors()) {
            $loginname = 'username';
            if (preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $this->loginname)) {
                $loginname = 'useremail';
            }
            $data = self::find()->where($loginname.' = :loginname',[":loginname" => $this->loginname])->one();
            //BCrypt虽然对同一个密码，每次生成的hash不一样，但是hash中包含了salt,在下次校验时，从hash中取出salt，salt跟password进行hash；得到的结果跟保存在DB中的hash进行比对
            if (!Yii::$app->getSecurity()->validatePassword($this->userpassword, $data->userpassword)) {
                $this->addError("userpassword", "用户名或者密码错误");
            }
        }
    }
    
    /* 两个表进行连接
     * 第一个参数为要关联的字表模型类名称，第二个参数指定通过主表的userid去关联子表的userid字段
     */
    public function getProfile() {
        return $this->hasOne(Profile::className(), ['userid' => 'userid']);
    }

    public function addUser($data, $scenario = 'adduser') {
        $this->scenario = $scenario;
        if ($this->load($data) && $this->validate()) {
            //$this->userpassword = md5($this->userpassword);
            //使用BCrypt算法加密密码
            $this->userpassword = Yii::$app->getSecurity()->generatePasswordHash($this->userpassword);
            $this->createtime = time();
            if ($this->save(false)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function reg($data) {
        $this->scenario = 'reg';
        $data['User']['username'] = 'jingxi_'.uniqid();
        $data['User']['userpassword'] = uniqid();

        if ($this->load($data) && $this->validate()) {
            $mailer = Yii::$app->mailer->compose('reguser', ['username' => $data['User']['username'], 'userpassword' => $data['User']['userpassword']]);
            $mailer->setFrom('1026251951@qq.com');
            $mailer->setTo($data['User']['useremail']);
            $mailer->setSubject('京西商城-用户注册');
            //if ($mailer->send() && $this->addUser($data, 'reg'))
            if ($mailer->Queue() && $this->addUser($data, 'reg'))
            {
                return TRUE;
            }
        }
        return FALSE;
    }


    public function getuserid($name) {
        $loginname = 'username';
        if (preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $name)) {
            $loginname = 'useremail';
        }
        $data = self::find()->where($loginname.' = :loginname ',[":loginname" => $name])->one();
        return $data->userid;
    }

    public function getUser() {
        $loginname = 'username';
        if (preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $this->loginname)) {
            $loginname = 'useremail';
        }
        $data = self::find()->where($loginname.' = :loginname ',[":loginname" => $this->loginname])->one();
        return $data;
    }

    public function login($data) {
        //设置验证规则场景
        $this->scenario = 'login';
        //validate()数据检验函数会自动调用rules()中的规则进行数据检验
        if ($this->load($data) && $this->validate()) {
            //session id 在 cookie保存的有效期(24*3600为保存一天,0为不保存)
            //$lifetime = $this->rememberMe ? 24*3600 : 0;
            //session_set_cookie_params($lifetime);
            //设置session
            //$session = Yii::$app->session;
            //$session['loginname'] = $this->loginname;
            //$session['isLogin'] = 1;
            //$session['userid'] = $this->getuserid($this->loginname);
            //return (bool)$session['isLogin'];
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 24*3600 : 0);
        }
        return FALSE;
    }

    /**
     * 根据给到的ID查询身份。
     *
     * @param string|integer $id 被查询的ID
     * @return IdentityInterface|null 通过ID匹配到的身份对象
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * 根据 token 查询身份。
     *
     * @param string $token 被查询的 token
     * @return IdentityInterface|null 通过 token 得到的身份对象
     * 一般用户restful接口应用
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //return static::findOne(['access_token' => $token]);
        return null;
    }

    /**
     * @return int|string 当前用户ID
     */
    public function getId()
    {
        return $this->userid;
    }

    /**
     * @return string 当前用户的（cookie）认证密钥
     */
    public function getAuthKey()
    {
        //return $this->auth_key;
        return NULL;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return true;
        //return $this->getAuthKey() === $authKey;
    }


}
