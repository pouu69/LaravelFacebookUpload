# Laravel Facebook image Upload
- Facebook 페이지와 개인 피드에 이미지를 업로드 할 수 있도록 도와준다.
- 한 포스팅에 여러 이미지를 동시에 올릴 수 있다.

#Installation
프로젝트에 있는 composer.json에 다음을 추가 하시거나, 

```` php
{
    "require": {
        "pouu69/laravel-facebook-upload": "^1.0"
    }
}
````
composer 를 이용하여 설치 할 수 있습니다.

`composer require pouu69/laravel-facebook-upload`

#ServiceProvider
`config/app.php`에 아래 와 같이 providers에 등록을 합니다.

```` php
'providers' => [
	pouu69\LaravelFacebookUpload\LaravelFacebookUploadServiceProvider::class,
]
````

#Facade (optional)
Facade 등록을 통해 alias를 사용하고 싶은 경우 다음과 같이 추가 하시면 됩니다.

```` php
'aliases' => [
    'FacebookUpload' => pouu69\LaravelFacebookUpload\LaravelFacebookUploadFacade::class,
];
````

#IoC container
Laravel Ioc container를 통하여 자동으로 dependency 한것들을 `LaravelFacebookUpload` 에 reslove할 수 있습니다.

```` php
// Directly from the IoC
$fb = App::make('pouu69\LaravelFacebookUpload\LaravelFacebookUpload');
// Or in PHP >= 5.5
$fb = app(pouu69\LaravelFacebookUpload\LaravelFacebookUpload::class);

// From a constructor
class FooClass {
    public function __construct(pouu69\LaravelFacebookUpload\LaravelFacebookUpload $fb) {
       // . . .
    }
}

// From a method
class BarClass {
    public function barMethod(pouu69\LaravelFacebookUpload\LaravelFacebookUpload $fb) {
       // . . .
    }
}

// Or even a closure
Route::get('/facebook/upload', function(pouu69\LaravelFacebookUpload\LaravelFacebookUpload $fb) {
    // . . .
});
````

# require
- "php": ">=5.5.0"
- "facebook/graph-sdk": "^5.0"
- "sammyk/laravel-facebook-sdk": "^3.0"

#사용법
##페이스북 페이지 리스트 가져오기
> 자신의 계정이 속한 페이스북 페이지 리스트를 가져올 수 있습니다.

```` php
// 페이스북 로그인 상태를 유지 하고
// session('facebook_access_token'); 으로 access_token을 저장 해논 상태 이후 사용한다.

$fb = app(pouu69\LaravelFacebookUpload\LaravelFacebookUpload::class);
/** @return array 페이지 리스트(안에 정보가 담겨있음) */
$pageList = $fb->getPageList();
````
##페이스북 피드에 이미지 업로드
> 개인 피드 또는 페이지에 이미지를 업로드 할 수 있게 도와주고, 한번의 포스팅으로 여러 이미지를 동시에 업로드 합니다.

```` php
// request Data set
$data = [
	"whereShare" : "", // 'me' 또는 'page' 로 구분,
	"accessId" : "", // 'me' 또는 getPageList() 를 통해 받은 page의 'access_token'
	"accessToken" : "" // 'me 또는 getPageList() 를 통해 받은 page의 'access_token',
	"message" : "" // feed에 보여질 메세지,
	"url" : [] // 업로드 할 이미지 url's 
];

$result = $fb->upload($data);
// result 반환 값
$result = [
            'status' => '', // 'done' 또는 'error'
            'message' => '',
            'data' => ''
        ];

````

##License
The MIT License (MIT). 
