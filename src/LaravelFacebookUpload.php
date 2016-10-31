<?php namespace pouu69\LaravelFacebookUpload;

use \SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class LaravelFacebookUpload{
    /** @var array response message */
    protected $resMsg = [];
    /** @var string 각 사용자의 access token requried */
    protected $ACCESS_TOKEN = '';
    /** @var string 페이지공유 일경우 ACCESS ID 가 필요requried */
    protected $ACCESS_ID = '';
    /** @var string me, page인지 알아야 함 */
    protected $WHERE_SHARE = '';
    /** @var string card id */
    protected $CARD_ID = '';
    /** @var string 전송할 메세지 */
    protected $MESSAGE = '';

    /** @var class facebook sdk 객체 */
    private $fbSDK;

    /** @var class session class */
    private $session;

    public function __construct(){
        $this->session = new LaravelFacebookSession;
    	$this->fbSDK = \App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');

    	$this->resMsg = [
            'status' => 'done',
            'message' => '',
            'data' => ''
        ];
    }

    /**
     * 개인 페북 엑세스토큰 설정
     * @param string $sessionName facebook 엑세스토큰 저장한 세션 이름
     */
    public function setTokenSession($sessionName){
        $this->session->set($sessionName);
    }

    /**
     * data setting
     * @param Array $data parameter data객체
     */
    protected function setData(array $data){
    	$data = (object)$data;

		$this->WHERE_SHARE = $data->whereShare;
		$this->ACCESS_ID = $data->accessId;
		$this->ACCESS_TOKEN = $data->accessToken; // me 일경우 $data->accessToken은 페북개인엑세스토큰값
		$this->MESSAGE = $data->message;
    }

    /**
     * facebook 업로드 요청           
     */
    public function upload(array $data){
    	$this->setData($data);
	$this->setTokenSession($data['fbSessionName']);
	    $batchData = $this->setBatchPhotoUpload($data['url']);
	    if(!$batchData) return $this->resMsg;

	    $attachMediaIds = $this->batchPhotoUpload($batchData);
	    if(!$attachMediaIds) return $this->resMsg;

	    $ret = $this->publishing($attachMediaIds);
	    if(!$ret)	return $this->resMsg;
	    else 		$this->setResMsg('done', '공유 완료', $ret);

	    // process 정상종료
        return $this->resMsg;
    }

    /**
     * 계정의 페이지들을 반환
     * @return array page list
     */
    public function getPageList(){
    	$pages = $this->account();

    	if($pages !== false){
    		$this->setResMsg('done', 'true',$pages);
    	}

        return $this->resMsg;
    }

    protected function makeEndPoint($wantJob){
    	return '/'.$this->ACCESS_ID.'/'.$wantJob;
    }

    /**
     * 계정 정보 속에 있는 페이지 정보
     * @return array 페이지 정보
     */
   	protected function account(){
		try {
			$endPoint = '/me/accounts';
			$resp = $this->fbSDK->get($endPoint, $this->session->get());
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			$this->setResMsg('error', $e->getMessage());
			return false;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			$this->setResMsg('error', $e->getMessage());
			return false;
		}

		$graphEdge = $resp->getGraphEdge();

		// page 정보를 봅아서 arrya로 변환 한다.
		$pages = [];
		foreach ($graphEdge as $key1 => $graphNode) {
	    	foreach($graphNode as $key2 => $pagesNode){
				if(is_object($pagesNode)){
					foreach($pagesNode as $key3 => $v){
						$pages[$key1][$key2][$key3] = $v;
					}
				}else{
					$pages[$key1][$key2] = $pagesNode;
				}
	    	}
		}

		return $pages;
    }

    /**
     * 로그인 사용자 permissin 가져오기
     * @return array permission's
     */
    public function getPermissions(){
        try{
            $endPoint = '/me/permissions';
            $resp = $this->fbSDK->get($endPoint,  $this->session->get());
            $graphEdge= $resp->getGraphEdge();

            $perms = [];
            foreach($graphEdge as $key => $val){
                $perm = [];
                foreach ($val as $k => $v) {
                    $perm[] = $v;
                }
                $perms[] = $perm;
            }
            return $perms;
        }catch(\Facebook\Exceptions\FacebookResponseException $e){
            $this->setResMsg('error', $e->getMessage());
            return false;
        }catch(\Facebook\Exceptions\FacebookSDKException $e) {
            $this->setResMsg('error', $e->getMessage());
            return false;
        }
    }

    /**
     * 최종적으로 feed에 퍼블리싱 하는 작업
     * @param  array  $attachMediaIds 퍼블리싱할 이미지들
     * @return object                 response
     */
    protected function publishing(array $attachMediaIds){
		try {
			$resp = $this->fbSDK->post($this->makeEndPoint('feed'), [
			 			'message' => $this->MESSAGE,
			 			'attached_media' => $attachMediaIds
					], $this->ACCESS_TOKEN);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			return $this->sendResponse('error', $e->getMessage(),null, 'publishing');
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			return $this->sendResponse('error', $e->getMessage(),null, 'publishing');
		}

		$graphNode = $resp->getGraphNode();

		return ['attachMediaIds' => $attachMediaIds, 'graphNode' => $graphNode['id'] ];
    }


    /**
     * upload 할 batch request를 생성한다.
     * @param array  $photos      	업로드 할 이미지
     * @param object|array $options 추가 옵션사항
     * @return array $data 			업로드할  이미지 배치 리퀘스트들 모음
     */
    protected function setBatchPhotoUpload(array $photos, $options=null){
    	try{
			$this->fbSDK->setDefaultAccessToken($this->ACCESS_TOKEN);
			$batchData = [];
			foreach($photos as $key => $photo){
				$batchData[] = $this->fbSDK->request('POST', $this->makeEndPoint('photos'), [
					      	'url' =>$photo,
					      	'published' => 'false'
					    ]);
			}
			return $batchData;
		}catch(\Exceptions $e){
			$this->setResMsg('error', $e->getMessage());
			return false;
		}
    }

    /**
     * 배치 데이터들을 일괄 업로드 한다.
     * @param  array  $batchData 배치 데이터
     * @return array             일괄 업로드 이후 반환 받은 각 이미지들의 ID
     */
    protected function batchPhotoUpload($batchData){
	    try {
			$responses = $this->fbSDK->sendBatchRequest($batchData);
			$attachMediaIds = [];

			foreach ($responses as $key => $response) {
				if ($response->isError()) {
					$e = $response->getThrownException();
				} else {
					$publishedId = $response->getGraphNode()['id'];
					$attachMediaIds[] = '{"media_fbid":"'.$publishedId.'"}';
				}
			}

			return $attachMediaIds;
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			return $this->sendResponse('error', $e->getMessage(),null, 'batchPhotoUpload');
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			return $this->sendResponse('error', $e->getMessage(),null, 'batchPhotoUpload');
		}

    }

    /**
     * response msg 를 전역적으로 설정하기 위하여
     * @param var $status 상태 코드
     * @param string $msg 상태 메세지
     */
    protected function setResMsg($status='done', $msg='', $data=null){
        $this->resMsg = [
            'status' => $status,
            'message' => $msg,
            'data' => $data
        ];
    }

    /**
     * 실패시 response하기 위하여
     * @param  string $status     상태값
     * @param  string $msg        상태 메세지
     * @param  array|object $data 전달할 데이터
     * @param  string $methodName 실패한 메서드이름
     * @return 		              response
     */
    protected function sendResponse($status='done', $msg='', $data=null, $methodName=''){
    	if($status === 'error'){
            \ServerReport::ERROR([
                'title' => '[facebook] 공유에러',
                'className' => 'FacebookController',
                'methodName' => $methodName,
                'messages' => $msg
            ]);
    	}
		$this->setResMsg($status, $msg, $data);
		return false;
    }

}
