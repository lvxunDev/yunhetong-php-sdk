# php SDK 接入快速上手

- 本demo基于 PHP 5.5,如是其他版本的请自行进行相应的修改。
- 我们为您编写了一份详细的 ```Demo``` 在 sdk 的 ```example``` 目录下
- 你可以查看详细的[文档](https://github.com/lvxunDev/yunhetong-php-sdk/wiki)
- 遇到问题可以先去看看我们的 [Issue](https://github.com/lvxunDev/yunhetong-php-sdk/issues)
- 或者也许你想看看 [phpDoc](https://lvxundev.github.io/yunhetong-php-sdk/phpDoc/phpDoc.html)

# 0x00 目录结构

```
phpSDK
|
|-------docs     // 一些说明文档
|-------example  // php SDK Demo 地址
|-------library  // yunhetong php SDK 核心包
|         |---- model   // 一些实体类
|         |---- Http.class.php
|         |---- LxAESHandler.class.php      // AES 加密相关的一个类，客户一般不需要使用
|         |---- LxRSAHandler.class.php      // RSA 加密相关的一个类，客户一般不需要使用
|         |---- LxSDKManager.class.php      // 客户最主要使用的一个类
|         |---- LxSecretManager.class.php   // 加解密管理类，客户一般不需要调用
|         |---- StringUtils.php             // 字符串处理的工具类
```

# 0x01 初始化 LxSDKManager

为了方便，这里我们建一个资源类```R.php```,并添加如下代码：

```php

$root_path = $_SERVER['DOCUMENT_ROOT'];
include_once($root_path . "/library/LxSDKManager.class.php");
include_once($root_path . "/library/model/User.php");
include_once($root_path . "/library/model/Actor.php");
include_once($root_path . "/library/model/Contract.php");

class R
{

    // 第三方应用的appId，这里换成您的 appId
    public static $appId = "";

    /**
     * @return LxSDKManager 返回SDKManager
     */
    public static function getLxSDKManager()
    {
        $current_path = realpath(dirname(__FILE__));
        return new LxSDKManager(self::$appId, $current_path . "\\key\\yhtSK.pem", $current_path . "\\key\\rsa_private_key_pkcs8.pem");
    }
}
```
其中公私玥参考公私玥相关的那篇文章。


# 0x02 导入用户
我们要导入用户并且获取 token
- 准备用户数据

在 R 类中添加如下代码

```php
    /**
     * 获取测试用户A
     * @return User
     */
    public static function getUserA()
    {
        $user = new User();
        $user->appId = self::$appId;          // 第三方应用的 appId
        $user->appUserId = 'phpTestUserA1';   // 用户在第三方应用的唯一标识
        $user->userType = 1;                  // 用户类型,1是个人，2是企业
        $user->cellNum = '11111111111';       // 电话号码，为1开头的11为数字
        $user->userName = 'TestA';            // 用户名称
        $user->certifyType = 2;               // 实名认证类型，1身份证2护照3军官证4营业执照5组织机构代码证
        $user->certifyNumber = '52059487';    // 用户实名认证时候的证件号码，可以是对应的身份证、营业执照、组织机构代码证或者其他证件号码，原则上不能大于 30 个字符
        $user->createSignature = "0";     // 是否自动创建签名，在导入用户并且当值为 1 时，会为导入的用户自动创建签名，0的话就不会，这个值只在用户第一次导入时有效
        return $user;
    }
```

- 导入用户

```php
$user = R::getUserA();
$sdk_manager = R::getLxSDKManager();
echo $sdk_manager->sync_get_token($user);
```

- 返回结果
正常会返回如下所示字符串

```json
{"code":200,"message":"true","subCode":200,"value":{"contractList":[{"id":1701061349385004,"status":"签署中","title":"测试合同标题40"},{"id":1701031046255028,"status":"签署中","title":"测试合同标题25"}],"token":"TGT-31356-4FZDJcQR3yK4IiaWIafnxQY0QAIoAI0SP6jja0VFY65PJ1S2W4-cas01.example.org"}}
```

然后将 token 返回给客户端，客户端再通过这个 token 去调用相应的SDK（比如js SDK 或 Android SDK 或 iOS SDK），去访问合同操作

# 0x03 生成合同
初始化 LxSDKManager 略，参考上面第一条。假设有个 A,B 两个人，A 要发起一份合同合同给 B，此时 A是合同的发起方， 也是合同的参与方。以此为例，代码如下
- 准备用户 B 信息
参考上面第二条用户 A 的信息，用户 B 的代码如下

```php
    /**
     * 获取测试用户A
     * @return User
     */
    public static function getUserB()
    {
        $user = new User();
        $user->appId = self::$appId;
        $user->appUserId = 'phpTestUserB';
        $user->userType = 1;
        $user->cellNum = '11111111122';
        $user->userName = 'TestB';
        $user->certifyType = 2;
        $user->certifyNumber = '52059487';
        $user->createSignature = "0";
        return $user;
    }
```

- 准备合同信息

```php
    /**
     * 创建测试合同
     * @return Contract
     */
    public static function getTestContract()
    {
        $contract = new Contract();
        $contract->appId = self::$appId;
        $contract->title = "测试合同标题" . date("s");   // 合同标题
        $contract->overtime = time() * 1000;   // 因为 java 的时间跟php的时间不大一样，所以这里 *1000
        $contract->defContractNo = "随便写";   // 自定义合同编号，方便对接客户对合同进行管理，可以随便写
        $contract->templateId = 123456;        // 设置合同模板 Id
        $contract->params = R::getContractParams();   // 这是模板占位符
        return $contract;
    }

    /**
     * 获取测试合同的参数
     * @return array
     */
    public static function getContractParams()
    {
        $params = array();
        $params["\${nameA}"] = "nameA";
        return $params;
    }
```
- 准备合同参与方

在刚才的用户A、B的基础上，我们可以生成合同的参与方

```php
   /**
     * 获取合同的参与方
     * @return array
     */
    public static function getActor()
    {
        return array(R::actorA(), R::actorB());
    }

    /**
     * 获取合同的参与方 A
     * @return Actor
     */
    public static function actorA()
    {
        $actor = new Actor();
        $actor->user = R::getUserA();
        $actor->locationName = "signA";   // 签名位置，在模板那边可以设置
        $actor->autoSign = 0;             // 是否自动签署合同0不自动签1自动签署
        return $actor;
    }

    /**
     * 获取合同的参与方 B
     * @return Actor
     */
    public static function actorB()
    {
        $actor = new Actor();
        $actor->user = R::getUserB();
        $actor->locationName = "signB";
        $actor->autoSign = 0;
        return $actor;
    }
```

- 生成合同

```php
$sdk_manager = R::getLxSDKManager();

echo $sdk_manager->sync_create_contract(R::getTestContract(), R::getActor());
```

- 返回结果
正常的话会返回如下所示字符串

```
{"code":200,"message":"true","subCode":200,"value":{"contractId":1701061352090008}}
```
将上一步得到的 token 和这里的 contractId 返回给客户端，即可用相应的 SDK（比如js SDK 或 Android SDK 或 iOS SDK），去进行合同的相关操作。

# 0x04 通过创建合同获取 token
有时候我们想在创建合同的同时也获取 Token，我们可以像下面这样
```php
$sdk_manager = R::getLxSDKManager();
echo $sdk_manager->sync_get_token_with_contract(R::getUserA(), R::getTestContract(), R::getActor());
```

正常的话会返回如下所示字符串
```json
{"code":200,"message":"true","subCode":200,"value":{"contractId":1701061349385004,"token":"TGT-31353-vpnotTbYFJ5wXoTUDzjSD9eVqZfzx9RZIsUhqGcEL5kjRcS6V6-cas01.example.org"}}

```


# 0x05 End
就是这么简单方便，更具体的例子请参考 example 里的Demo。






