<?php
namespace app\modules\models;
use \yii\db\ActiveRecord;
use Yii;
class Admin extends ActiveRecord implements \yii\web\IdentityInterface
{
    //默认不自动登录
    public $rememberMe = TRUE;
    public $confirmPassword;

    public static function tableName() {
        //{{%}} = shop_ (代替了db.php中配置的表前缀)
        return "{{%admin}}";
    }
    
    public function attributeLabels() {
        return [
            'adminuser' => '管理员账号',
            'adminemail' => '管理员邮箱',
            'adminpassword' => '管理员密码',
            'confirmPassword' => '确认密码',
        ];
    }

    public function rules() {
        return [
            ['adminuser','required','message'=>'管理员账号不能为空', 'on' => ['login', 'seekpassword', 'adminchangepassword', 'addmanage', 'changeemail']],
            ['adminuser', 'unique', 'message' => '管理员账号已被注册', 'on' => 'addmanage'],
            ['adminpassword','required','message'=>'管理员密码不能为空', 'on' => ['login', 'adminchangepassword', 'addmanage', 'changeemail']],
            ['rememberMe','boolean', 'on' => 'login'],
            ['adminpassword','validatePassword', 'on' => ['login', 'changeemail']],
            ['adminemail', 'unique', 'message' => '管理员邮箱已被注册', 'on' => ['addmanage', 'changeemail']],
            ['adminemail', 'required', 'message' => '管理员邮箱不能为空', 'on' => ['seekpassword', 'addmanage', 'changeemail']],
            ['adminemail', 'email', 'message' => '邮箱格式不正确', 'on' => ['seekpassword', 'addmanage', 'changeemail']],
            ['adminemail', 'validateEmail', 'on' => 'seekpassword'],
            ['confirmPassword', 'required', 'message' => '确认密码不能为空', 'on' => ['adminchangepassword', 'addmanage']],
            ['confirmPassword', 'compare', 'compareAttribute' => 'adminpassword', 'message' => '两次密码不一致', 'on' => ['adminchangepassword', 'addmanage']],
            ['rememberMe', 'safe']
        ];
    }

    public function validatePassword() {
        //如果前面验证没有错误在进行对数据库的查询
        if (!$this->hasErrors()) {
            $data = self::find()->where('adminuser = :user',[":user"=>$this->adminuser])->one();
            //BCrypt虽然对同一个密码，每次生成的hash不一样，但是hash中包含了salt,在下次校验时，从hash中取出salt，salt跟password进行hash；得到的结果跟保存在DB中的hash进行比对
            if (!Yii::$app->getSecurity()->validatePassword($this->adminpassword, $data->adminpassword)) {
                $this->addError("adminpassword", "用户名或者密码错误");
            }
        }
    }

    public function validateEmail() {
        if (!$this->hasErrors()) {
            $data = self::find()->where('adminuser = :user and adminemail = :email', [':user' => $this->adminuser, ':email' => $this->adminemail])->one();
            if (is_null($data)) {
                $this->addError('adminemail', '用户名和邮箱不对应');
            }
        }
    }

    public function login($data) {
        //设置验证规则场景
        $this->scenario = 'login';
        //validate()数据检验函数会自动调用rules()中的规则进行数据检验
        if ($this->load($data) && $this->validate()) {
            /*
            //session id 在 cookie保存的有效期(24*3600为保存一天,0为不保存)
            $lifetime = $this->rememberMe ? 24*3600 : 0;
            session_set_cookie_params($lifetime);
            //设置session
            $session = Yii::$app->session;
            $session['admin'] = [
              'adminuser' => $this->adminuser,
              'isLogin' => 1,
            ];
            //更新登录时间和ip
            $this->updateAll(['logintime'=>time(),'loginip'=>ip2long(Yii::$app->request->userIP)],'adminuser = :user',[':user'=>$this->adminuser]);
            return (bool)$session['admin']['isLogin'];
            */
            $admin = self::find()->where('adminuser = :adminuser', [':adminuser' => $this->adminuser])->one();
            //更新登录时间和ip
            $this->updateAll(['logintime'=>time(),'loginip'=>ip2long(Yii::$app->request->userIP)],'adminuser = :user',[':user'=>$this->adminuser]);
            return Yii::$app->admin->login($admin, 24*3600);
        }
        return false;
    }

    public function seekpassword($data) {
        //设置验证规则场景
        $this->scenario = 'seekpassword';
        if ($this->load($data) && $this->validate()) {
             //发送邮件
            $time = time();
            $token = $this->createToken($data['Admin']['adminuser'], $time);
            $mailer = Yii::$app->mailer->compose('seekpassword', ['adminuser' => $this->adminuser, 'time' => $time, 'token' => $token]);
            $mailer->setFrom('xiaomengxiaoqiu@163.com');
            $mailer->setTo($data['Admin']['adminemail']);
            $mailer->setSubject('京西商城-找回密码');
            if ($mailer->send()) {
                return true;
            }
        }
        return false;
    }

    public function createToken($adminuser, $time) {
        return md5(md5($adminuser).base64_encode(Yii::$app->request->userIP).md5($time));
    }

    public function adminChangePassword($data) {
        $this->scenario = 'adminchangepassword';
        if ($this->load($data) && $this->validate()) {
           return $this->updateAll(['adminpassword' => md5($this->confirmPassword)], 'adminuser = :user', [':user' => $this->adminuser]);
        }
        return false;
    }

    public function addManage($data) {
        $this->scenario = 'addmanage';
        if ($this->load($data) && $this->validate()) {
            $this->createtime = time();
            //$this->adminpassword = md5($this->adminpassword);
            //$this->confirmPassword = md5($this->confirmPassword);
            //使用BCrypt算法加密密码
            $this->adminpassword = Yii::$app->getSecurity()->generatePasswordHash($this->adminpassword);
            //save方法第一个参数为false是不进行validate验证
            if ($this->save(false)) {
                return true;
            }
            return false;
        }
        return false;
    }
    
    public function changeEmail($data) {
        $this->scenario = 'changeemail';
        if ($this->load($data) && $this->validate()) {
             return $this->updateAll(['adminemail' => $this->adminemail], 'adminuser = :user', [':user' => $this->adminuser]);
        }
        return false;
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
        return $this->adminid;
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

