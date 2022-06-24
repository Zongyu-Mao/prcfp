<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\Api\Globalization\NotificationController;
use App\Http\Controllers\Api\Globalization\UserAdviseController;
use App\Http\Controllers\Api\WordbankController;
use App\Http\Controllers\Api\Assistant\AssistantController;
use App\Http\Controllers\Api\Assistant\PolysemantController;
use App\Http\Controllers\Api\Assistant\AvatarController;
use App\Http\Controllers\Api\Assistant\SynonymController;
use App\Http\Controllers\Api\Basic\BasicController;
use App\Http\Controllers\Api\Wordbank\KeywordDetailController;
use App\Http\Controllers\Api\Document\DocumentController;
use App\Http\Controllers\Api\Announcement\AnnouncementController;
use App\Http\Controllers\Api\Management\CommitteeController;
use App\Http\Controllers\Api\Management\Committee\CommitteeMemberController;
use App\Http\Controllers\Api\Management\CommitteeDocumentController;
use App\Http\Controllers\Api\Management\Committee\CommitteeRoleController;
use App\Http\Controllers\Api\Management\Committee\CommitteeRoleReactController;
use App\Http\Controllers\Api\Management\Committee\CommitteeInformController;
use App\Http\Controllers\Api\Management\Committee\CommitteeSurveillanceController;
use App\Http\Controllers\Api\Management\Surveillance\SurveillanceController;
use App\Http\Controllers\Api\Management\Surveillance\SurveillanceMarkTypeController;
use App\Http\Controllers\Api\Management\Surveillance\SurveillanceMarkDisposeWayController;
use App\Http\Controllers\Api\Picture\PictureDelete\PictureDeleteController;
use App\Http\Controllers\Api\Picture\PictureController;
use App\Http\Controllers\Api\Picture\PictureEdit\PictureEditController;
use App\Http\Controllers\Api\Picture\Content\ContentPictureController;
use App\Http\Controllers\Api\Picture\MindMap\MindMapController;
use App\Http\Controllers\Api\Vote\VoteController;
use App\Http\Controllers\Api\Vote\VoteCreateController;
use App\Http\Controllers\Api\Vote\VoteRecordController;
use App\Http\Controllers\Api\Document\DocumentDirectoryController;
use App\Http\Controllers\Api\HomeRecommendController;
use App\Http\Controllers\Api\Personal\FriendController;
use App\Http\Controllers\Api\Personal\PersonalSetController;
use App\Http\Controllers\Api\Personal\PersonalNotificationController;
use App\Http\Controllers\Api\Personal\PersonalDynamicController;
use App\Http\Controllers\Api\Personal\PersonalContentController;
use App\Http\Controllers\Api\Personal\PersonalRelationshipController;
use App\Http\Controllers\Api\Personal\FriendActivityInvitationController;
use App\Http\Controllers\Api\Personal\PrivateLetterController;
use App\Http\Controllers\Api\PersonalController;
use App\Http\Controllers\Api\Personal\MyWorks\MyEntryController;
use App\Http\Controllers\Api\Personal\MyWorks\MyArticleController;
use App\Http\Controllers\Api\Personal\MyWorks\MyExamController;
use App\Http\Controllers\Api\Personal\MyWorks\MyGroupController;
use App\Http\Controllers\Api\Personal\MyInform\MyInformController;
use App\Http\Controllers\Api\Personal\MySuit\MySuitController;
use App\Http\Controllers\Api\Personal\MyMedal\MyMedalController;
use App\Http\Controllers\Api\Personal\MyProp\MyPropController;
use App\Http\Controllers\Api\Personal\MyProp\PropInitializationController;
use App\Http\Controllers\Api\Personal\MyFocus\MyFocusController;
use App\Http\Controllers\Api\Personal\MyFocus\MyCollectionController;
use App\Http\Controllers\Api\Personal\PrivateMedal\PrivateMedalController;
use App\Http\Controllers\Api\Personal\PrivateMedal\PrivateMedalRecordController;
use App\Http\Controllers\Api\PersonnelController;
use App\Http\Controllers\Api\Personnel\RoleController;
use App\Http\Controllers\Api\Personnel\RoleRight\RoleRightController;
use App\Http\Controllers\Api\Personnel\Role\RoleModifyController;
use App\Http\Controllers\Api\Personnel\Role\RoleApplyController;
use App\Http\Controllers\Api\Personnel\Role\RoleFriendController;
use App\Http\Controllers\Api\Personnel\LevelController;
use App\Http\Controllers\Api\Personnel\Level\LevelModifyController;
use App\Http\Controllers\Api\Personnel\MilestoneController;
use App\Http\Controllers\Api\Personnel\Milestone\MilestoneModifyController;
use App\Http\Controllers\Api\Personnel\PropController;
use App\Http\Controllers\Api\Personnel\Prop\PropModifyController;
use App\Http\Controllers\Api\Personnel\MedalSuitController;
use App\Http\Controllers\Api\Personnel\MedalController;
use App\Http\Controllers\Api\Personnel\Medal\MedalModifyController;
use App\Http\Controllers\Api\Personnel\MedalSuit\MedalSuitModifyController;
use App\Http\Controllers\Api\Personnel\BehaviorController;
use App\Http\Controllers\Api\Personnel\Behavior\BehaviorModifyController;
use App\Http\Controllers\Api\Personnel\Inform\BasicInformController;
use App\Http\Controllers\Api\Personnel\Inform\MessageInformController;
use App\Http\Controllers\Api\Personnel\Inform\JudgementInformController;
use App\Http\Controllers\Api\Personnel\InformController;
use App\Http\Controllers\Api\Personnel\Inform\InformDetailController;
use App\Http\Controllers\Api\Personnel\Inform\InformReactController;
use App\Http\Controllers\Api\Personnel\Inform\PunishSuitController;
use App\Http\Controllers\Api\ClassificationController;
use App\Http\Controllers\Api\Classification\ClassificationDetailController;
use App\Http\Controllers\Api\Classification\ClassificationAdditionController;
use App\Http\Controllers\Api\Classification\ClassificationBasicContentsController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\Organization\GroupController;
use App\Http\Controllers\Api\Organization\Group\PrimaryGroupController;
use App\Http\Controllers\Api\Organization\Group\GroupUserController;
use App\Http\Controllers\Api\Organization\GroupCreateController;
use App\Http\Controllers\Api\Organization\Personnel\MemberPositionController;
use App\Http\Controllers\Api\Organization\GroupDoc\GroupDocCreateController;
use App\Http\Controllers\Api\Organization\GroupDoc\GroupDocController;
use App\Http\Controllers\Api\Organization\GroupDoc\GroupDocCommentController;
use App\Http\Controllers\Api\CommonShare\AbstractController;
use App\Http\Controllers\Api\CommonShare\KeywordController;
use App\Http\Controllers\Api\CommonShare\ReferenceController;
use App\Http\Controllers\Api\CommonShare\ContentFocusAndCollectController;
use App\Http\Controllers\Api\Event\EventController;
use App\Http\Controllers\Api\Cooperation\CooperationAssignController;
use App\Http\Controllers\Api\Cooperation\CooperationDiscussionController;
use App\Http\Controllers\Api\Cooperation\CooperationMessageController;
use App\Http\Controllers\Api\Cooperation\CooperationVoteController;
use App\Http\Controllers\Api\Cooperation\CooperationVersionController;
use App\Http\Controllers\Api\Cooperation\ContributeValueController;
use App\Http\Controllers\Api\Cooperation\CooperationLeaderController;
use App\Http\Controllers\Api\Review\ReviewRecordController;
use App\Http\Controllers\Api\Review\ReviewAdviseController;
use App\Http\Controllers\Api\Resort\ResortCreateController;
use App\Http\Controllers\Api\Resort\ResortSupportController;
use App\Http\Controllers\Api\Resort\ResortCommentController;
use App\Http\Controllers\Api\Discussion\DiscussionCreateController;
use App\Http\Controllers\Api\Discussion\OpponentController;
use App\Http\Controllers\Api\Discussion\AdvisementController;
use App\Http\Controllers\Api\Discussion\DiscussionCommentController;
use App\Http\Controllers\Api\Debate\DebateCreateController;
use App\Http\Controllers\Api\Debate\DebateRefereeController;
use App\Http\Controllers\Api\Debate\DebateGiveUpController;
use App\Http\Controllers\Api\Debate\DebateGiveLikeController;
use App\Http\Controllers\Api\Debate\DebateMessageController;
use App\Http\Controllers\Api\Debate\DebateExpirationController;
use App\Http\Controllers\Api\EncyclopediaRecommendController;
use App\Http\Controllers\Api\Encyclopedia\EntryCreateController;
use App\Http\Controllers\Api\Encyclopedia\EntryController;
use App\Http\Controllers\Api\Encyclopedia\Recommend\EntryRecommendByUserController;
use App\Http\Controllers\Api\Encyclopedia\Entry\EntryContentController;
use App\Http\Controllers\Api\Encyclopedia\Entry\ChapterOperations\ChapterMoveController;
use App\Http\Controllers\Api\Encyclopedia\Entry\ChapterOperations\ChapterOperateController;
use App\Http\Controllers\Api\Encyclopedia\Entry\AmbiguityController;
use App\Http\Controllers\Api\Encyclopedia\EntryCooperationController;
use App\Http\Controllers\Api\Encyclopedia\EntryReviewController;
use App\Http\Controllers\Api\Encyclopedia\EntryReview\ReviewCreateController;
use App\Http\Controllers\Api\Encyclopedia\EntryDiscussionController;
use App\Http\Controllers\Api\Encyclopedia\EntryResortController;
use App\Http\Controllers\Api\Encyclopedia\EntryDebateController;
use App\Http\Controllers\Api\Encyclopedia\EntryHistoryController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\Publication\ArticleController;
use App\Http\Controllers\Api\Publication\Article\ArticleCreateController;
use App\Http\Controllers\Api\Publication\Article\ArticleContentController;
use App\Http\Controllers\Api\Publication\Article\ArticlePartController;
use App\Http\Controllers\Api\Publication\Recommend\ArticleRecommendByUserController;
use App\Http\Controllers\Api\Publication\Article\ChapterOperations\ArticleChapterMoveController;
use App\Http\Controllers\Api\Publication\Article\PartOperation\ArticlePartMoveController;
use App\Http\Controllers\Api\Publication\Article\ChapterOperations\ArticleChapterOperateController;
use App\Http\Controllers\Api\Publication\Article\PartOperation\ArticlePartOperateController;
use App\Http\Controllers\Api\Publication\ArticleCooperationController;
use App\Http\Controllers\Api\Publication\ArticleCooperation\Personnel\ArticleCooperationVoteController;
use App\Http\Controllers\Api\Publication\ArticleReviewController;
use App\Http\Controllers\Api\Publication\ArticleReview\ArticleReviewCreateController;
use App\Http\Controllers\Api\Publication\ArticleResortController;
use App\Http\Controllers\Api\Publication\ArticleDiscussionController;
use App\Http\Controllers\Api\Publication\ArticleDebateController;
use App\Http\Controllers\Api\Publication\ArticleHistoryController;
use App\Http\Controllers\Api\ExaminationController;
use App\Http\Controllers\Api\Examination\ExamController;
use App\Http\Controllers\Api\Examination\ExamCreateController;
use App\Http\Controllers\Api\Examination\PartStem\PartStemModifyController;
use App\Http\Controllers\Api\Examination\Question\QuestionModifyController;
use App\Http\Controllers\Api\Examination\PartStem\PartStemCreateController;
use App\Http\Controllers\Api\Examination\Question\QuestionCreateController;
use App\Http\Controllers\Api\Examination\PartStem\PartStemMoveController;
use App\Http\Controllers\Api\Examination\Question\QuestionMoveController;
use App\Http\Controllers\Api\Examination\ExamOver\ExamOverController;
use App\Http\Controllers\Api\Examination\ExamCooperationController;
use App\Http\Controllers\Api\Examination\ExamCooperation\Personnel\ExamCooperationVoteController;
use App\Http\Controllers\Api\Examination\ExamReviewController;
use App\Http\Controllers\Api\Examination\ExamReview\ExamReviewCreateController;
use App\Http\Controllers\Api\Examination\ExamResortController;
use App\Http\Controllers\Api\Examination\ExamDiscussionController;
use App\Http\Controllers\Api\Examination\ExamDebateController;
use App\Http\Controllers\Api\Examination\ExamHistoryController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\Uploader\ContentImageUploadController;
use App\Http\Controllers\Api\Cooperation\CooperationMemberController;
use App\Http\Controllers\Api\Search\SearchController;
use App\Http\Controllers\Api\CommonShare\ExtendController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\SanctumAuthController;
use App\Http\Controllers\EmailVerifiedController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


     
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('test', [TestController::class,'test']);
});


Route::group(['middleware' => 'api'], function ($router) {
	Route::post('login', [SanctumAuthController::class, 'signin']);
	Route::post('register', [SanctumAuthController::class, 'signup']);
	Route::post('verify', [EmailVerifiedController::class, 'verifyConfirm']);
	Route::post('email-verified', [EmailVerifiedController::class, 'emailVerifiedHandle']);
	Route::post('auth/reset-password-request', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
	Route::post('auth/change-password', [ChangePasswordController::class, 'passwordResetProcess']);
    // Route::post('login', [AuthController::class, 'login']);
    // Route::post('register', [AuthController::class, 'register']);
    // 编辑器的图片上传,需要验证 先放在这里
	Route::post('contentImageUpload/{scope}/{obj}/{id}',[ContentImageUploadController::class,'contentImageUpload']);
});

Route::group(['middleware' => 'auth:sanctum'], function ($router) {
    // Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('user-property', [SanctumAuthController::class, 'userProperty']);
    Route::post('logout', [SanctumAuthController::class, 'logout']);
    Route::prefix('wordbank')->group(function(){
		Route::post('keywords',[WordbankController::class,'keywords']);
		Route::post('keyword',[KeywordDetailController::class,'keyword']);
    });
    // 主内容帮助区域
    Route::prefix('assistant')->group(function(){
		Route::post('checkRoleRight',[AssistantController::class,'checkRoleRight']);
		Route::post('extend_reading',[AssistantController::class,'extend_reading']);
		Route::post('synonyms',[SynonymController::class,'synonyms']);
		Route::post('synonymModify',[SynonymController::class,'synonymModify']);
		Route::post('polysemants',[PolysemantController::class,'polysemants']);
		Route::post('polysemantModify',[PolysemantController::class,'polysemantModify']);
		Route::post('create_polysemant_check',[PolysemantController::class,'create_polysemant_check']);
		Route::post('avatarSwitch',[AvatarController::class,'avatarSwitch']);
    });
    // 主内容公共区域区域
    Route::prefix('basic')->group(function(){
		Route::post('contents',[BasicController::class,'contents']);
    });
    Route::prefix('globalization')->group(function(){
    	//全域内容
		Route::post('global_notification',[NotificationController::class,'global_notification']);
		Route::post('global_notifications',[NotificationController::class,'global_notifications']);
		Route::post('notificationModify',[NotificationController::class,'notificationModify']);
		// 建议
		Route::post('global_advise',[UserAdviseController::class,'global_advise']);
		Route::post('global_advises',[UserAdviseController::class,'global_advises']);
		Route::post('new_advise',[UserAdviseController::class,'new_advise']);
		Route::post('new_comment',[UserAdviseController::class,'new_comment']);
		
    });
    Route::prefix('document')->group(function(){
    	//规则文档
		Route::post('documents',[DocumentController::class,'documents']);
		Route::post('getDocument',[DocumentController::class,'getDocument']);
		Route::post('getDocumentContentModifyKey',[DocumentController::class,'getDocumentContentModifyKey']);
		Route::post('releaseDocumentContentModifyKey',[DocumentController::class,'releaseDocumentContentModifyKey']);
		Route::post('documentCreate',[DocumentController::class,'documentCreate']);
		Route::post('documentModify',[DocumentController::class,'documentModify']);
		Route::post('directories',[DocumentDirectoryController::class,'directories']);
		Route::post('directoryCreate',[DocumentDirectoryController::class,'directoryCreate']);
		Route::post('directoryModify',[DocumentDirectoryController::class,'directoryModify']);
    });
    Route::prefix('management')->group(function(){
		Route::post('committees',[CommitteeController::class,'committees']);
		Route::post('all_committees',[CommitteeController::class,'all_committees']);
		Route::post('managerUpdate',[CommitteeController::class,'managerUpdate']);
		Route::post('committee',[CommitteeController::class,'committee']);
		Route::post('judgeRole',[CommitteeMemberController::class,'judgeRole']);
		Route::post('managerAward',[CommitteeMemberController::class,'managerAward']);
		Route::post('memberQuit',[CommitteeMemberController::class,'memberQuit']);
		Route::post('committeeCreate',[CommitteeController::class,'committeeCreate']);
		Route::post('committeeDocuments',[CommitteeDocumentController::class,'committeeDocuments']);
		Route::post('committeeDocument',[CommitteeDocumentController::class,'committeeDocument']);
		Route::post('committeeDocumentCreate',[CommitteeDocumentController::class,'committeeDocumentCreate']);
		Route::post('committeeDocumentCommentCreate',[CommitteeDocumentController::class,'committeeDocumentCommentCreate']);
		Route::post('committeeDocumentModify',[CommitteeDocumentController::class,'committeeDocumentModify']);
		Route::post('committeeRoles',[CommitteeRoleController::class,'committeeRoles']);
		Route::post('committeeRoleRecord',[CommitteeRoleReactController::class,'committeeRoleRecord']);
		Route::post('committeeRoleReact',[CommitteeRoleReactController::class,'committeeRoleReact']);
		Route::post('roleReactRecord',[CommitteeRoleController::class,'roleReactRecord']);
		Route::post('committeeInforms',[CommitteeInformController::class,'committeeInforms']);
		Route::post('committeeSurveillances',[CommitteeSurveillanceController::class,'committeeSurveillances']);
		Route::post('committeeSurveillanceMarks',[CommitteeSurveillanceController::class,'committeeSurveillanceMarks']);
		Route::post('markReactRecord',[CommitteeSurveillanceController::class,'markReactRecord']);
		Route::post('committeeMarkReact',[CommitteeSurveillanceController::class,'committeeMarkReact']);
		Route::post('markDetail',[CommitteeSurveillanceController::class,'markDetail']);
		Route::post('committeeSurveillanceWarnings',[CommitteeSurveillanceController::class,'committeeSurveillanceWarnings']);
		Route::post('surveillance',[SurveillanceController::class,'surveillance']);
		Route::post('surveillanceRequest',[SurveillanceController::class,'surveillanceRequest']);
		Route::post('passSurveillance',[SurveillanceController::class,'passSurveillance']);
		Route::post('markWarning',[SurveillanceController::class,'markWarning']);
		Route::post('repealWarning',[SurveillanceController::class,'repealWarning']);
		Route::post('markTypes',[SurveillanceMarkTypeController::class,'markTypes']);
		Route::post('markTypeModify',[SurveillanceMarkTypeController::class,'markTypeModify']);
		Route::post('markDisposeWays',[SurveillanceMarkDisposeWayController::class,'markDisposeWays']);
		Route::post('markDisposeWayModify',[SurveillanceMarkDisposeWayController::class,'markDisposeWayModify']);
    });
    Route::prefix('picture')->group(function(){
		Route::post('pictureDeleteOnUnexpectedDestroy',[PictureDeleteController::class,'pictureDeleteOnUnexpectedDestroy']);
		// 脑图直接转到图片下面，不在主内容下了
		Route::post('getMindMap',[MindMapController::class,'getMindMap']);
		Route::post('modifyMindMap',[MindMapController::class,'modifyMindMap']);
		Route::post('elementContentCheck',[MindMapController::class,'elementContentCheck']);
		// 以下开启图片板块
		Route::post('pictureIndex',[PictureController::class,'pictureIndex']);
		Route::post('featuredPicturesEditIndex',[PictureEditController::class,'featuredPicturesEditIndex']);
		Route::post('featuredPictureEdit',[PictureEditController::class,'featuredPictureEdit']);
		Route::post('featuredPictureIntroductionEdit',[PictureEditController::class,'featuredPictureIntroductionEdit']);
		Route::post('picturesUnderclass',[PictureController::class,'picturesUnderclass']);
		Route::post('feturedPictureDetail',[PictureController::class,'feturedPictureDetail']);
		Route::post('entryLinkThird',[PictureEditController::class,'entryLinkThird']);
		Route::post('contentPictures',[ContentPictureController::class,'contentPictures']);
		Route::post('linkPictures',[ContentPictureController::class,'linkPictures']);//考虑到统一，虽然仅link到entry 但是仍放到这里
		Route::post('pictureDelete',[ContentPictureController::class,'pictureDelete']);
		
    });
    Route::prefix('vote')->group(function(){
		Route::post('votes',[VoteController::class,'votes']);
		Route::any('getVote',[VoteController::class,'getVote']);
		Route::any('voteCreate',[VoteCreateController::class,'voteCreate']);
		Route::post('voteModify',[VoteController::class,'voteModify']);
		Route::post('voting',[VoteRecordController::class,'voting']);
		Route::post('voteFinish',[VoteController::class,'voteFinish']);
    });
    // 搜索
    Route::prefix('search')->group(function(){
		Route::post('search',[SearchController::class,'search']);
		Route::post('searchContent',[SearchController::class,'searchContent']);
    });
	
    // Api页面的推荐内容
	Route::post('getHomeRecommends',[HomeRecommendController::class,'getHomeRecommends']);
	Route::post('announcements',[AnnouncementController::class,'announcements']);
    // 得到我的好友***********************-----------------------------------------------------------
    Route::post('getFriends',[FriendController::class,'getFriends']);
    Route::post('getBasicInviteRecord',[FriendController::class,'getBasicInviteRecord']);
    //用户首页
	Route::post('personalHomepage',[PersonalController::class,'personalHomepage']);
	//延伸阅读
	Route::post('extend_reading',[ExtendController::class,'extend_reading']);
	Route::post('extend_delete',[ExtendController::class,'extend_delete']);
	Route::post('extend_check',[ExtendController::class,'extend_check']);
	Route::prefix('personal')->group(function(){
		//用户页面，设置页面
		Route::post('setting',[PersonalSetController::class,'setting']);
		// 提交设置
		Route::post('set',[PersonalSetController::class,'personalSet']);
		//得到兴趣专业页面
		Route::post('set/getSpecialities',[PersonalSetController::class,'getSpecialities']);
		//设置兴趣专业页面
		Route::post('set/specialityModify',[PersonalSetController::class,'specialityModify']);
		// 用户通知页
		Route::post('notification',[PersonalNotificationController::class,'notification']);
		// 获取动态
		Route::post('getDynamics',[PersonalDynamicController::class,'getDynamics']);
		// 获取自己的内容
		Route::post('ownContents',[PersonalContentController::class,'ownContents']);
		// 用户删除通知*********************************
		Route::post('personalNotificationDelete',[PersonalNotificationController::class,'personalNotificationDelete']);
		// 关注用户
		Route::post('userFocus',[PersonalRelationshipController::class,'userFocus']);
		// 确认用户存在
		Route::post('userMessageCheck', [PersonalRelationshipController::class, 'userMessageCheck']);
		// 添加好友申请页面
		Route::post('friendApplication',[PersonalRelationshipController::class,'friendApplication']);
		// 同意添加好友申请
		Route::post('friendApplicationStand',[PersonalRelationshipController::class,'friendApplicationStand']);
		// 好友的活动（协作）邀请////////*****************************/////////////
		Route::post('friendActivityInvitation',[FriendActivityInvitationController::class,'friendActivityInvitation']);
		// 好友发送私信
		Route::post('privateLetterSend',[PrivateLetterController::class,'privateLetterSend']);
		// 私信页面
		Route::post('privateLetter',[PrivateLetterController::class,'privateLetter']);
		Route::post('privatePartLetters',[PrivateLetterController::class,'privatePartLetters']);
		// 我的举报信息
		// 注意这个不属于myworks
		Route::post('myInforms',[MyInformController::class,'myInforms']);
		// 我的功章套,注意这个不属于myworks
		Route::post('mySuits',[MySuitController::class,'mySuits']);
		// 我的功章套件,注意这个不属于myworks
		Route::post('myMedals',[MyMedalController::class,'myMedals']);
		// 我的道具
		Route::post('myProps',[MyPropController::class,'myProps']);
		// 我的关注
		Route::post('myFocuses',[MyFocusController::class,'myFocuses']);
		// 我的收藏夹
		Route::post('myCollections',[MyCollectionController::class,'myCollections']);
		// 私人功章
		Route::post('privateMedals',[PrivateMedalController::class,'privateMedals']);
		// 创建私人功章页面
		Route::post('privateMedalCreate',[PrivateMedalController::class,'privateMedalCreate']);
		// 修改功章属性
		Route::post('medalGiving',[PrivateMedalController::class,'medalGiving']);
		// 修改功章属性
		Route::post('privateMedalRecord',[PrivateMedalRecordController::class,'privateMedalRecord']);
	});
	// 人事首页
	Route::post('personnel',[PersonnelController::class,'personnel']);
	Route::prefix('personnel')->group(function(){
		// 显示用户角色页面
		Route::post('role',[RoleController::class,'roles']);
		Route::post('roleRights',[RoleRightController::class,'roleRights']);
		Route::post('roleRightModify',[RoleRightController::class,'roleRightModify']);
		// 申请/推举角色
		Route::post('roleApply',[RoleApplyController::class,'roleApply']);
		Route::post('roleElect',[RoleApplyController::class,'roleElect']);
		Route::post('getAllFriends',[FriendController::class,'getAllFriends']);
		Route::post('getRoleRecords',[RoleFriendController::class,'getRoleRecords']);
		// 角色属性
		Route::post('roleModify',[RoleModifyController::class,'roleModify']);
		// 删除角色属性
		Route::post('roleDelete',[RoleModifyController::class,'roleDelete']);

		// 显示用户等级页面
		Route::post('level',[LevelController::class,'levels']);
		// 创建用户等级页面
		// Route::post('levelCreate',[LevelModifyController::class,'levelCreate']);
		// 修改等级属性
		Route::post('levelModify',[LevelModifyController::class,'levelModify']);
		// 删除
		Route::post('levelDelete',[LevelModifyController::class,'levelDelete']);

		// 显示里程碑页面
		Route::post('milestone',[MilestoneController::class,'milestones']);
		// 创建里程碑页面
		// Route::post('milestoneCreate',[MilestoneModifyController::class,'milestoneCreate']);
		// 修改里程碑属性
		Route::post('milestoneModify',[MilestoneModifyController::class,'milestoneModify']);
		// 删除里程碑
		Route::post('milestoneDelete',[MilestoneModifyController::class,'milestoneDelete']);

		// 显示道具页面
		Route::post('prop',[PropController::class,'props']);
		// 创建道具页面
		// Route::post('propCreate',[PropModifyController::class,'propCreate']);
		// 修改道具属性
		Route::post('propModify',[PropModifyController::class,'propModify']);
		// 删除道具
		Route::post('propDelete',[PropModifyController::class,'propDelete']);

		// 显示功章套件页面
		Route::post('medalSuit',[MedalSuitController::class,'medalSuit']);
		// 显示功章套件页面
		Route::post('medalSuitDetail',[MedalSuitController::class,'medalSuitDetail']);
		// 显示功章页面
		Route::post('medal',[MedalController::class,'medal']);
		// 创建功章页面
		Route::post('medalCreate',[MedalModifyController::class,'medalCreate']);
		// 创建功章套件页面
		Route::post('medalSuitCreate',[MedalSuitModifyController::class,'medalSuitCreate']);
		// 修改功章属性
		Route::post('medalModify',[MedalModifyController::class,'medalModify']);
		// 获得正文内容的编辑锁
		Route::post('getMedalModifyKey',[MedalModifyController::class,'getMedalModifyKey']);
		// 释放正文内容的编辑锁
		Route::post('releaseMedalModifyKey',[MedalModifyController::class,'releaseKey']);
		// 删除功章属性
		Route::post('medalDelete',[MedalModifyController::class,'medalDelete']);
		// 显示（热度）行为页面
		Route::post('behavior',[BehaviorController::class,'behaviors']);
		// 创建（热度）行为页面
		// Route::post('behaviorCreate',[BehaviorModifyController::class,'behaviorCreate']);
		// 修改（热度）行为属性
		Route::post('behaviorModify',[BehaviorModifyController::class,'behaviorModify']);
		// 删除（热度）行为
		Route::post('behaviorDelete',[BehaviorModifyController::class,'behaviorDelete']);
		// 刷新（热度）行为
		Route::post('behaviorsInit',[BehaviorController::class,'behaviorsInit']);
		// 主内容的举报（大师专属权利）
		Route::post('basicInform',[BasicInformController::class,'basicInform']);
		Route::post('basicContentCheck',[BasicInformController::class,'basicContentCheck']);
		// 裁决内容的举报（对应用户权利）
		Route::post('judgementInform',[JudgementInformController::class,'judgementInform']);
		Route::post('judgementContentCheck',[JudgementInformController::class,'judgementContentCheck']);
		// 留言的举报（基本用户权利）
		Route::post('messageInform',[MessageInformController::class,'messageInform']);
		Route::post('messageContentCheck',[MessageInformController::class,'messageContentCheck']);
		// 举报信息首页
		Route::post('inform',[InformController::class,'informs']);
		// 举报信息首页
		Route::post('inform/detail',[InformDetailController::class,'informDetail']);
		// 返回举报章
		Route::post('punishSuit',[PunishSuitController::class,'punishSuit']);
		// 通过主内容举报信息
		Route::post('inform/react',[InformReactController::class,'react']);
	});

	// 得到分类
	Route::post('classification',[ClassificationController::class,'classification']);
	// 分类详情页
	Route::post('underclass',[ClassificationDetailController::class,'underclass']);
	// 中层分类详情页
	Route::post('middleclass',[ClassificationDetailController::class,'middleclass']);
	// 通过id获取下一级子类
	Route::post('getClassChildrenById',[ClassificationAdditionController::class,'getClassChildrenById']);
	Route::prefix('classification')->group(function(){
		// 获取底层分类对应主内容
		Route::post('basicContents',[ClassificationBasicContentsController::class,'basicContents']);
		Route::post('getClassOrganizations',[ClassificationBasicContentsController::class,'organizations']);
	});
	//添加内容分类
	Route::post('classification/addition',[ClassificationAdditionController::class,'addition']);
	//修改内容分类
	Route::post('classification/modify',[ClassificationAdditionController::class,'modify']);

	// 组织版块开始****************************************************************************************************
	//组织首页
	Route::post('organization',[OrganizationController::class,'organization']);
	//组织详情页（我的某组织）
	Route::post('group',[GroupController::class,'group']);
	//组织成员页
	Route::post('groupUser',[GroupUserController::class,'groupUser']);
	
	//变更成员身份~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	Route::post('groupMemberPositionChange',[MemberPositionController::class,'positionChange']);
	// 组织文档的创建
	Route::post('groupDocCreate',[GroupDocCreateController::class,'docCreate']);
	// 文档副首页
	Route::post('groupDoc',[GroupDocController::class,'groupDoc']);
	// 文档详情
	Route::post('groupDocDetail',[GroupDocController::class,'detail']);
	// 文档评论
	Route::post('groupDocComment',[GroupDocCommentController::class,'commentAdd']);

	Route::prefix('organization')->group(function(){
		//组织的创建
		Route::post('create',[GroupCreateController::class,'groupCreate']);
		// 更改文档正文
		Route::post('docModify',[GroupDocController::class,'modify']);
		Route::post('primaryGroup',[PrimaryGroupController::class,'primaryGroup']); //主组织设置

	});

	// ************************************************************************************************************
	// Route::prefix('focus')->group(function(){
	// 	//得到基本的focus数据
	// 	Route::post('getFocus',[ContentFocusAndCollectController::class,'getFocus']);
	// });
    // 摘要修改
	Route::post('abstractModify',[AbstractController::class,'abstractModify']);
    //关键词的修改
	Route::post('keywordsModify',[KeywordController::class,'keywordsModify']);
	//参考文献的添加
	Route::post('referenceAdd',[ReferenceController::class,'referenceAdd']);
	//参考文献的修改
	Route::post('referenceModify',[ReferenceController::class,'referenceModify']);
	//参考文献的删除
	Route::post('referenceDelete',[ReferenceController::class,'referenceDelete']);
	//关注
	Route::post('contentFocus',[ContentFocusAndCollectController::class,'contentFocus']);
	Route::prefix('events')->group(function(){
		// 得到相关的事件列表
		Route::post('getEvents',[EventController::class,'getEvents']);
	});
	Route::prefix('cooperation')->group(function(){
		//协作计划节点计划的编辑
		Route::post('assign',[CooperationAssignController::class,'assign']);
		//协作计划讨论面板路由
		Route::post('discuss',[CooperationDiscussionController::class,'discussion']);
		//协作计划用户留言处理路由
		Route::post('message',[CooperationMessageController::class,'message']);
		//协作计划成员对用户留言的回复
		Route::post('message_reply',[CooperationMessageController::class,'message_reply']);
		//协作计划申请加入路由
		Route::post('join',[CooperationVoteController::class,'join']);
		//邀请好友加入协作计划
		Route::post('cooperationMemberInvite',[CooperationMemberController::class,'cooperationMemberInvite']);
		//协作小组事务性投票
		Route::post('affairVote',[CooperationVoteController::class,'affairVote']);
		//协作小组请退组员 组长清退 暂时不投票
		Route::post('memberFired',[CooperationLeaderController::class,'memberFired']);
		// 放弃自管理
		Route::post('manageQuit',[CooperationLeaderController::class,'manageQuit']);
		//成员退出小组
		Route::post('teamQuit',[CooperationVoteController::class,'teamQuit']);
		// 成为自管理员
		Route::post('beContentManager',[CooperationLeaderController::class,'beContentManager']);
		//协作小组授予组长的投票*************************************************************
		Route::post('leaderAwarded/{crew_id}/{cooperation_id}',[CooperationLeaderController::class,'leaderAwarded']);
		//弹劾小组组长的投票*****************************************************
		Route::post('leaderImpeach/{id}',[CooperationVoteController::class,'leaderImpeach']);
		
		//成员对投票结果的提交
		Route::post('voteStandPoint',[CooperationVoteController::class,'voteStandPoint']);
		//版本更新
		Route::post('versionUpdate',[CooperationVersionController::class,'versionUpdate']);
		//内容降级
		Route::post('levelLower',[CooperationVersionController::class,'levelLower']);
		//简易升级
		Route::post('simpleUpgrade',[CooperationVersionController::class,'simpleUpgrade']);
		//贡献值
		Route::post('contributeValueAssign',[ContributeValueController::class,'contributeValueAssign']);
	});
	Route::prefix('review')->group(function(){
		//评审计划反对意见的处理
		Route::post('opponent',[ReviewRecordController::class,'opponent']);
		//评审计划拒绝反对意见的处理
		Route::post('oppose_reject',[ReviewRecordController::class,'oppose_reject']);
		//评审计划接受反对意见的处理
		Route::post('oppose_accept',[ReviewRecordController::class,'oppose_accept']);
		//评审计划建议意见的处理
		Route::post('advise',[ReviewAdviseController::class,'advise']);
		//评审计划接受建议的处理
		Route::post('advise_accept',[ReviewAdviseController::class,'advise_accept']);
		//评审计划拒绝建议的处理
		Route::post('advise_reject',[ReviewAdviseController::class,'advise_reject']);
		//评审计划支持意见的处理
		Route::post('support',[ReviewRecordController::class,'support']);
		//评审计划中立意见的处理
		Route::post('neutrality',[ReviewRecordController::class,'neutrality']);
		//评审计划支持及中立区的回复的处理
		Route::post('discuss_reply',[ReviewRecordController::class,'discuss_reply']);
		//关闭评审计划
		Route::post('reviewTerminate',[ReviewRecordController::class,'reviewTerminate']);
	});

	Route::prefix('resort')->group(function(){
		//求助内容的创建
		Route::post('resort_create',[ResortCreateController::class,'resort_create']);
		//帮助内容的创建和提交
		Route::post('support',[ResortSupportController::class,'support']);
		//帮助内容的拒绝
		Route::post('support_reject',[ResortSupportController::class,'support_reject']);
		//帮助内容的接受
		Route::post('support_accept',[ResortSupportController::class,'support_accept']);
		//帮助内容的普通评论
		Route::post('support_comment',[ResortCommentController::class,'support_comment']);
		//帮助内容普通评论的回复
		Route::post('comment_reply',[ResortCommentController::class,'comment_reply']);
	});

	Route::prefix('discussion')->group(function(){
		//讨论页面的创建
		Route::post('discussion_create',[DiscussionCreateController::class,'discussion_create']);
		//讨论拒绝反对意见的处理
		Route::post('oppose_reject',[OpponentController::class,'oppose_reject']);
		//讨论拒绝建议的处理
		Route::post('advise_reject',[AdvisementController::class,'advise_reject']);
		//讨论接受反对意见的处理
		Route::post('oppose_accept',[OpponentController::class,'oppose_accept']);
		//讨论接受建议的处理
		Route::post('advise_accept',[AdvisementController::class,'advise_accept']);
		//普通讨论回复的处理
		Route::post('discussion_reply',[DiscussionCommentController::class,'discussion_reply']);
	});
	Route::prefix('debate')->group(function(){
		//攻辩的创建（辩题、立论和开篇陈词）
		Route::post('debate_create',[DebateCreateController::class,'debate_create']);
		//辩方立论和开篇陈词
		Route::post('BOS_create',[DebateCreateController::class,'BOS_create']);
		//攻方自由辩论
		Route::post('AFD_create',[DebateCreateController::class,'AFD_create']);
		//辩方自由辩论
		Route::post('BFD_create',[DebateCreateController::class,'BFD_create']);
		//攻方的总结陈词
		Route::post('ACS_create',[DebateCreateController::class,'ACS_create']);
		//辩方的总结陈词
		Route::post('BCS_create',[DebateCreateController::class,'BCS_create']);
		//成为裁判
		Route::post('asTheReferee',[DebateRefereeController::class,'asTheReferee']);
		// 辩论的放弃、攻方
		Route::post('debateGiveUp',[DebateGiveUpController::class,'debateGiveUp']);
		//支持或不支持参与方
		Route::post('debateGiveLike',[DebateGiveLikeController::class,'debateGiveLike']);
		//裁判的分析
		Route::post('debateAnalyse',[DebateRefereeController::class,'debateAnalyse']);
		//裁判的总结
		Route::post('debate_summary',[DebateRefereeController::class,'debate_summary']);
		//辩论页的留言创建
		Route::post('debate_message',[DebateMessageController::class,'debate_message']);
		//辩论页的评论的回复
		Route::post('debate_message_reply',[DebateMessageController::class,'debate_message_reply']);
		// 复核辩论过期
		Route::post('debate_expiration',[DebateExpirationController::class,'debate_expiration']);
	});
	// encyclopedia页面的推荐内容
	Route::post('getEncyclopediaRecommends',[EncyclopediaRecommendController::class,'getEncyclopediaRecommends']);
	// 创建词条、表单上传
	Route::post('entryCreate',[EntryCreateController::class,'create']);
	// encyclopedia页面的推荐内容
	Route::post('getEntryDetail/{id}/{title}',[EntryController::class,'getEntry']);
	// entry页面的相关编辑
	//用户推荐词条
	Route::post('entryRecommendByUser',[EntryRecommendByUserController::class,'entryRecommendByUser']);
	// 获得正文内容的编辑锁
	Route::post('getEntryContentModifyKey',[EntryContentController::class,'getEntryContentModifyKey']);
	// 释放正文内容的编辑锁
	Route::post('releaseEntryContentModifyKey',[EntryContentController::class,'releaseKey']);
	//正文章节内容的编辑
	Route::post('entryContentModify',[EntryContentController::class,'entryContentModify']);
	// 章节操作，前移
	Route::post('entryChapterMove',[ChapterMoveController::class,'entryChapterMove']);
	// 章节操作，添加章节
	Route::post('entryChapterAdd',[ChapterOperateController::class,'entryChapterAdd']);
	// 章节操作，删除
	Route::post('entryChapterDelete',[ChapterOperateController::class,'entryChapterDelete']);
	// encyclopedia页面的词条协作 cooperation内容--------------------------------------------------------
	Route::post('getEntryCooperation/{id}/{title}',[EntryCooperationController::class,'getEntryCooperation']);
	Route::prefix('encyclopedia')->group(function(){
		// 歧义的处理
		Route::post('ambiguity',[AmbiguityController::class,'ambiguity']);
		Route::post('content_check',[AmbiguityController::class,'content_check']);
		//协作小组请退组员 组长清退 暂时不投票
		Route::post('cooperation/memberFired',[CooperationLeaderController::class,'memberFired']);
		//协作小组授予组长的投票*************************************************************
		Route::post('cooperation/leaderAwarded/{crew_id}/{cooperation_id}',[CooperationLeaderController::class,'leaderAwarded']);
		//弹劾小组组长的投票*****************************************************
		Route::post('cooperation/leaderImpeach/{id}',[CooperationVoteController::class,'leaderImpeach']);
		//词条的评审页面-------------------------------------------------------------------------------------
		Route::post('entryReview',[EntryReviewController::class,'entryReview']);
		//词条评审的创建
		Route::post('review/create',[ReviewCreateController::class,'create']);
		//词条的讨论页面*****************************************************
		Route::post('entryDiscussion',[EntryDiscussionController::class,'entryDiscussion']);
		//词条的求助页************************************************************
		Route::post('entryResort',[EntryResortController::class,'entryResort']);
		// 得到本词条所有的攻辩计划
		Route::post('entryDebate/{id}/{title}',[EntryDebateController::class,'entryDebate']);
		// 得到完整的单攻辩计划
		Route::post('debate/debate',[EntryDebateController::class,'debate']);
		// 词条的历史页面
		Route::post('entryHistory/{id}/{title}',[EntryHistoryController::class,'entryHistory']);
	});
	// Publication Article 著作板块开始***************************************************************************
	//著作首页
	Route::post('getPublicationRecommends',[PublicationController::class,'getPublicationRecommends']);
	//著作详情页
	Route::post('getArticleDetail/{id}/{articleTitle}',[ArticleController::class,'articleDetail']);
	// 创建著作
	Route::post('articleCreate',[ArticleCreateController::class,'create']);
	// 获得正文内容的编辑锁
	Route::post('getArticleContentModifyKey',[ArticleContentController::class,'getArticleContentModifyKey']);
	// 释放正文内容的编辑锁
	Route::post('releaseArticleContentModifyKey',[ArticleContentController::class,'releaseKey']);
	//著作正文章节内容的编辑
	Route::post('publication/article/articleContentModifyPage/{id}',[ArticleContentController::class,'articleContentModifyPage']);
	//著作正文章节内容的编辑
	Route::post('articleContentModify',[ArticleContentController::class,'articleContentModify']);
	//用户推荐著作
	Route::post('articleRecommendByUser',[ArticleRecommendByUserController::class,'articleRecommendByUser']);
	// 著作章节操作，前移
	Route::post('articleChapterMove',[ArticleChapterMoveController::class,'articleChapterMove']);
	// 著作章节操作，添加章节
	Route::post('articleChapterAdd',[ArticleChapterOperateController::class,'articleChapterAdd']);
	// 著作章节操作，删除
	Route::post('articleChapterDelete',[ArticleChapterOperateController::class,'articleChapterDelete']);
	//著作的协作计划展示页面*******************************
	Route::post('getArticleCooperation/{id}/{article_title}',[ArticleCooperationController::class,'articleCooperation']);
	Route::prefix('publication')->group(function(){
		// 获得正文分部的编辑锁
		Route::post('getArticlePartModifyKey',[ArticlePartController::class,'getArticlePartModifyKey']);
		// 释放正文内容的编辑锁
		Route::post('releaseArticlePartModifyKey',[ArticlePartController::class,'releaseKey']);
		//著作正文章节内容的编辑
		Route::post('getPartContents',[ArticleController::class,'getPartContents']);
		Route::post('articlePartModify',[ArticlePartController::class,'articlePartModify']);
		// 著作分部操作
		Route::post('articlePartMove',[ArticlePartMoveController::class,'articlePartMove']);
		// 著作添加part
		Route::post('articlePartAdd',[ArticlePartOperateController::class,'articlePartAdd']);
		Route::post('articlePartDelete',[ArticlePartOperateController::class,'articlePartDelete']);
		//弹劾小组组长的投票
		Route::post('cooperation/leaderImpeach/{id}',[ArticleCooperationVoteController::class,'leaderImpeach']);
		//著作的求助页
		Route::post('articleResort/{id}/{articleTitle}',[ArticleResortController::class,'articleResort']);
		//词条评审的创建
		Route::post('review/create',[ArticleReviewCreateController::class,'create']);
		//著作的讨论页面*************************
		Route::post('articleDiscussion/{id}/{articleTitle}',[ArticleDiscussionController::class,'articleDiscussion']);
		//著作的辩论页
		Route::post('articleDebate/{id}/{title}',[ArticleDebateController::class,'articleDebate']);
		// 得到完整的单攻辩计划
		Route::post('debate/debate',[ArticleDebateController::class,'debate']);
		// 著作的历史页面
		Route::post('articleHistory/{id}/{title}',[ArticleHistoryController::class,'articleHistory']);
	});
	//著作的评审页面
	Route::post('articleReview/{article_id}/{article_title}',[ArticleReviewController::class,'articleReview']);

	// Examination Exam 试卷板块开始***************************************************************************
	//首页
	Route::post('getExaminationRecommends',[ExaminationController::class,'getExaminationRecommends']);
	//首页
	Route::post('examCreate',[ExamCreateController::class,'create']);
	//详情页
	Route::post('getExamDetail/{id}/{examTitle}',[ExamController::class,'examDetail']);
	// 获得材料的编辑锁
	Route::post('getExamStemModifyKey',[PartStemModifyController::class,'getExamStemModifyKey']);
	// 释放材料的编辑锁
	Route::post('releaseExamStemModifyKey',[PartStemModifyController::class,'releaseKey']);
	//编辑材料
	Route::post('examPartStemModify',[PartStemModifyController::class,'partStemModify']);
	// 获得题目的编辑锁
	Route::post('getQuestionModifyKey',[QuestionModifyController::class,'getQuestionModifyKey']);
	// 释放题目的编辑锁
	Route::post('releaseQuestionModifyKey',[QuestionModifyController::class,'releaseQuestionModifyKey']);
	//编辑题目
	Route::post('questionModify',[QuestionModifyController::class,'questionModify']);
	//添加材料,第一次创建需要examid，从材料添加，不需要id
	Route::post('examPartStemCreate',[PartStemCreateController::class,'partStemCreate']);
	// 问题的建立
	Route::post('questionCreate',[QuestionCreateController::class,'questionCreate']);
	// 材料操作
	Route::post('partStemMove',[PartStemMoveController::class,'partStemMove']);
	// 问题操作
	Route::post('questionMove',[QuestionMoveController::class,'questionMove']);
	// 上传分数
	Route::post('uploadScore',[ExamOverController::class,'uploadScore']);
	// 上传评级
	Route::post('totalUpdate',[ExamOverController::class,'totalUpdate']);
	//协作计划展示页面*******************************
	Route::post('getExamCooperation/{id}/{exam_title}',[ExamCooperationController::class,'examCooperation']);
	//弹劾小组组长的投票*************
	Route::post('examination/cooperation/leaderImpeach/{id}',[ExamCooperationVoteController::class,'leaderImpeach']);
	//评审页面*************************************************************
	Route::post('examReview/{exam_id}/{exam_title}',[ExamReviewController::class,'examReview']);
	//评审的创建
	Route::post('examination/review/create',[ExamReviewCreateController::class,'create']);
	//求助页
	Route::post('examResort/{id}/{examTitle}',[ExamResortController::class,'examResort']);
	//讨论页面*************************
	Route::post('examDiscussion/{id}/{examTitle}',[ExamDiscussionController::class,'examDiscussion']);
	//辩论页
	Route::post('examDebate/{id}/{title}',[ExamDebateController::class,'examDebate']);
	// 得到完整的单攻辩计划
	Route::post('examination/debate/debate',[ExamDebateController::class,'debate']);
	// 历史页面
	Route::post('examHistory/{id}/{title}',[ExamHistoryController::class,'examHistory']);
	// 图片上传
	Route::post('upload/{scope}/{modify_id}',[UploadController::class,'upload']);
	
});
