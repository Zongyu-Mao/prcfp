<?php

namespace App\Providers;

use Illuminate\Auth\Events\Reistered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // 新的用户注册
        'App\Events\Auth\UserRegisterdEvent' => [
            'App\Listeners\Auth\UserRegisterdListener',
        ],
        // 用户的积分变化
        'App\Events\Personal\Credit\UserCreditChangedEvent' => [
            'App\Listeners\Personal\Credit\UserCreditChangedListener',
        ],
        // 以下picture
        'App\Events\Picture\PictureCreatedEvent' => ['App\Listeners\Picture\PictureCreatedListener',],
        'App\Events\Picture\PictureGiveLikeEvent' => ['App\Listeners\Picture\PictureGiveLikeListener',],
        'App\Events\Picture\PictureTemperatureRecordAddedEvent' => ['App\Listeners\Picture\PictureTemperatureRecordAddedListener',],
        'App\Events\Picture\PictureTemperatureUpdatedEvent' => ['App\Listeners\Picture\PictureTemperatureUpdatedListener',],
        'App\Events\Picture\PictureEntryLinkedEvent' => ['App\Listeners\Picture\PictureEntryLinkedListener',],

        // 网站文档的创建
        'App\Events\Document\DocumentCreatedEvent' => [
            'App\Listeners\Document\DocumentCreatedListener',
        ],
        // 网站文档的修改
        'App\Events\Document\DocumentModifiedEvent' => ['App\Listeners\Document\DocumentModifiedListener',],
        // 文档分类
        'App\Events\Document\DocumentDirectoryModifiedEvent' => ['App\Listeners\Document\DocumentDirectoryModifiedListener',],
        // 网站投票的创建
        'App\Events\Vote\VoteCreatedEvent' => ['App\Listeners\Vote\VoteCreatedListener',],
        // 网站投票记录的创建
        'App\Events\Vote\VoteRecordCreatedEvent' => [
            'App\Listeners\Vote\VoteRecordCreatedListener',
        ],
        // 网站投票的结束
        'App\Events\Vote\VoteFinishedEvent' => ['App\Listeners\Vote\VoteFinishedListener',],

        // 以下开始管理组相关事件
        // 角色权限相关，申请、推举
        'App\Events\Management\Role\RoleAppliedEvent' => ['App\Listeners\Management\Role\RoleAppliedListener'],
        'App\Events\Management\Role\RoleApplyReactEvent' => ['App\Listeners\Management\Role\RoleApplyReactListener'],
        'App\Events\Management\Role\RoleElectedEvent' => ['App\Listeners\Management\Role\RoleElectedListener'],
        'App\Events\Management\Role\RoleElectReactEvent' => ['App\Listeners\Management\Role\RoleElectReactListener'],
        // 角色清退
        'App\Events\Management\Role\RoleJudgedEvent' => ['App\Listeners\Management\Role\RoleJudgedListener'],
        // 巡查
        'App\Events\Management\Surveillance\SurveillanceEvent' => ['App\Listeners\Management\Surveillance\SurveillanceListener'],
        'App\Events\Management\Surveillance\SurveillanceArticleEvent' => ['App\Listeners\Management\Surveillance\SurveillanceArticleListener'],
        'App\Events\Management\Surveillance\SurveillanceExamEvent' => ['App\Listeners\Management\Surveillance\SurveillanceExamListener'],
        // 标记
        'App\Events\Management\Surveillance\MarkEvent' => ['App\Listeners\Management\Surveillance\MarkListener'],
        'App\Events\Management\Surveillance\MarkArticleEvent' => ['App\Listeners\Management\Surveillance\MarkArticleListener'],
        'App\Events\Management\Surveillance\MarkExamEvent' => ['App\Listeners\Management\Surveillance\MarkExamListener'],
        // 警示
        'App\Events\Management\Surveillance\WarningEvent' => ['App\Listeners\Management\Surveillance\WarningListener'],
        'App\Events\Management\Surveillance\WarningArticleEvent' => ['App\Listeners\Management\Surveillance\WarningArticleListener'],
        'App\Events\Management\Surveillance\WarningExamEvent' => ['App\Listeners\Management\Surveillance\WarningExamListener'],
        // 管理组type和way
        'App\Events\Management\Surveillance\TypeCreatedEvent' => ['App\Listeners\Management\Surveillance\TypeCreatedListener'],
        'App\Events\Management\Surveillance\WayCreatedEvent' => ['App\Listeners\Management\Surveillance\WayCreatedListener'],
        'App\Events\Management\Surveillance\TypeModyfiedEvent' => ['App\Listeners\Management\Surveillance\TypeModyfiedListener'],
        'App\Events\Management\Surveillance\WayModyfiedEvent' => ['App\Listeners\Management\Surveillance\WayModyfiedListener'],
        // 权限的得到和撤销以及管理组的进出

        // 管理员更新事件（单独放了）
        'App\Events\Encyclopedia\EntryManagerUpdatedEvent' => ['App\Listeners\Encyclopedia\EntryManagerUpdatedListener',],
        'App\Events\Publication\ArticleManagerUpdatedEvent' => ['App\Listeners\Publication\ArticleManagerUpdatedListener',],
        'App\Events\Examination\ExamManagerUpdatedEvent' => ['App\Listeners\Examination\ExamManagerUpdatedListener',],
        'App\Events\Organization\GroupManagerUpdatedEvent' => ['App\Listeners\Organization\GroupManagerUpdatedListener',],


        // 用户好友申请事件
        'App\Events\Personal\Relationship\UserFriendApplicationCreatedEvent' => [
            'App\Listeners\Personal\Relationship\UserFriendApplicationCreatedListener',
        ],
        // 同意用户好友申请事件
        'App\Events\Personal\Relationship\UserFriendApplicationAgreedEvent' => [
            'App\Listeners\Personal\Relationship\UserFriendApplicationAgreedListener',
        ],
        // 拒绝用户好友申请事件
        'App\Events\Personal\Relationship\UserFriendApplicationRejectedEvent' => [
            'App\Listeners\Personal\Relationship\UserFriendApplicationRejectedListener',
        ],
        // 好友活动邀请事件
        'App\Events\Personal\Relationship\FriendActivityInvitationCreatedEvent' => [
            'App\Listeners\Personal\Relationship\FriendActivityInvitationCreatedListener',
        ],
        // 好友对邀请活动的回复事件
        'App\Events\Personal\Relationship\FriendActivityInvitationRepliedEvent' => [
            'App\Listeners\Personal\Relationship\FriendActivityInvitationRepliedListener',
        ],
        // 好友私信发送事件
        'App\Events\Personal\Relationship\FriendPrivateLetterSentEvent' => [
            'App\Listeners\Personal\Relationship\FriendPrivateLetterSentListener',
        ],
        // 好友私信回复事件
        'App\Events\Personal\Relationship\FriendPrivateLetterRepliedEvent' => [
            'App\Listeners\Personal\Relationship\FriendPrivateLetterRepliedListener',
        ],
        // 勋章赠送事件
        'App\Events\Personal\PrivateMedal\PrivateMedalGivenEvent' => [
            'App\Listeners\Personal\PrivateMedal\PrivateMedalGivenListener',
        ],

        // 热度行为的创建
        'App\Events\Personnel\Behavior\BehaviorCreatedEvent' => [
            'App\Listeners\Personnel\Behavior\BehaviorCreatedListener',
        ],
        // 角色等//创建和更改
        'App\Events\Personnel\Role\RoleModifiedEvent' => ['App\Listeners\Personnel\Role\RoleModifiedListener',],
        'App\Events\Personnel\Level\LevelModifiedEvent' => ['App\Listeners\Personnel\Level\LevelModifiedListener',],
        'App\Events\Personnel\Milestone\MilestoneModifiedEvent' => ['App\Listeners\Personnel\Milestone\MilestoneModifiedListener',],
        'App\Events\Personnel\Prop\PropModifiedEvent' => ['App\Listeners\Personnel\Prop\PropModifiedListener',],
        // 举报相关
        // 功章套的创建
        'App\Events\Personnel\MedalSuit\MedalSuitCreatedEvent' => [
            'App\Listeners\Personnel\MedalSuit\MedalSuitCreatedListener',
        ],
        // 功章套件的创建
        'App\Events\Personnel\Medal\MedalCreatedEvent' => [
            'App\Listeners\Personnel\Medal\MedalCreatedListener',
        ],
        // 举报主内容的通过和驳回
        'App\Events\Personnel\Inform\InformOperate\InformOperateRecordAddedEvent' => [
            'App\Listeners\Personnel\Inform\InformOperate\InformOperateRecordAddedListener',
        ],
        // 举报裁决内容的通过和驳回
        'App\Events\Personnel\Inform\InformOperate\JudgementInformOperateRecordAddedEvent' => [
            'App\Listeners\Personnel\Inform\InformOperate\JudgementInformOperateRecordAddedListener',
        ],
        // 举报留言内容的通过和驳回
        'App\Events\Personnel\Inform\InformOperate\MessageInformOperateRecordAddedEvent' => [
            'App\Listeners\Personnel\Inform\InformOperate\MessageInformOperateRecordAddedListener',
        ],
        // 举报结果的写入punishRecord
        'App\Events\Personnel\Punish\PunishRecordAddedEvent' => [
            'App\Listeners\Personnel\Punish\PunishRecordAddedListener',
        ],

        // 新的内容分类添加事件
        'App\Events\Classification\ClassificationAddedEvent' => [
            'App\Listeners\Classification\ClassificationAddedListener',
        ],
        // 新的内容分类修改事件
        'App\Events\Classification\ClassificationModifiedEvent' => [
            'App\Listeners\Classification\ClassificationModifiedListener',
        ],

        // 管理组相关
        // 标记应对
        'App\Events\Management\Surveillance\MarkReactedEvent' => ['App\Listeners\Management\Surveillance\MarkReactedListener',],
        'App\Events\Management\Surveillance\ArticleMarkReactedEvent' => ['App\Listeners\Management\Surveillance\ArticleMarkReactedListener',],
        'App\Events\Management\Surveillance\ExamMarkReactedEvent' => ['App\Listeners\Management\Surveillance\ExamMarkReactedListener',],
        'App\Events\Management\Surveillance\GroupMarkReactedEvent' => ['App\Listeners\Management\Surveillance\GroupMarkReactedListener',],

        // 新的词条创建触发事件
        'App\Events\Encyclopedia\EntryCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryCreatedListener',
        ],
        // 词条热度增加事件
        'App\Events\Encyclopedia\Recommend\EntryTemperatureRecordAddEvent' => ['App\Listeners\Encyclopedia\Recommend\EntryTemperatureRecordAddListener',],
        // 百科热度更新事件
        'App\Events\Encyclopedia\Recommend\EntryTemperatureUpdatedEvent' => [
            'App\Listeners\Encyclopedia\Recommend\EntryTemperatureUpdatedListener',
        ],
        // 百科推荐更新事件
        'App\Events\Encyclopedia\Recommend\EntryRecommendationUpdatedEvent' => [
            'App\Listeners\Encyclopedia\Recommend\EntryRecommendationUpdatedListener',
        ],
        // 词条浏览量更新
        'App\Events\Encyclopedia\EntryViewsUpdatedEvent' => [
            'App\Listeners\Encyclopedia\EntryViewsUpdatedListener',
        ],
        // 词条关注
        'App\Events\Encyclopedia\Entry\Focus\EntryFocusedEvent' => [
            'App\Listeners\Encyclopedia\Entry\Focus\EntryFocusedListener',
        ],
        // 词条取消关注
        'App\Events\Encyclopedia\Entry\Focus\EntryFocusCanceledEvent' => [
            'App\Listeners\Encyclopedia\Entry\Focus\EntryFocusCanceledListener',
        ],
        // 词条收藏
        'App\Events\Encyclopedia\Entry\Focus\EntryCollectedEvent' => [
            'App\Listeners\Encyclopedia\Entry\Focus\EntryCollectedListener',
        ],
        // 词条取消收藏
        'App\Events\Encyclopedia\Entry\Focus\EntryCollectCanceledEvent' => [
            'App\Listeners\Encyclopedia\Entry\Focus\EntryCollectCanceledListener',
        ],

        // 词条修改摘要触发事件
        'App\Events\Encyclopedia\EntrySummaryModifiedEvent' => [
            'App\Listeners\Encyclopedia\EntrySummaryModifiedListener',
        ],
        // 词条正文内容第一次创建的实践
        'App\Events\Encyclopedia\EntryContentFirstCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryContentFirstCreatedListener',
        ],
        // 著作章节内容删除的事件
        'App\Events\Encyclopedia\Entry\EntryContentDeletedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryContentDeletedListener',
        ],
        // 词条修改了关键词的事件
        'App\Events\Encyclopedia\Entry\EntryKeywordModifiedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryKeywordModifiedListener',
        ],
        // 词条新增编辑的事件
        'App\Events\Encyclopedia\Entry\EntryContentModifiedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryContentModifiedListener',
        ],
        // 不再触发新的著作第一次编辑正文触发事件，改到著作正文内容创建触发事件（著作内容是分章的，所以每章创建都会触发事件）
        'App\Events\Encyclopedia\Entry\EntryContentCreatedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryContentCreatedListener',
        ],
        // 词条新增参考文献的事件
        'App\Events\Encyclopedia\Entry\EntryReference\EntryReferenceAddEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryReference\EntryReferenceAddListener',
        ],
        // 词条修改参考文献的事件
        'App\Events\Encyclopedia\Entry\EntryReference\EntryReferenceModifiedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryReference\EntryReferenceModifiedListener',
        ],
        // 词条修改参考文献的事件
        'App\Events\Encyclopedia\Entry\EntryReference\EntryReferenceDeletedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryReference\EntryReferenceDeletedListener',
        ],
        // 词条添加词条延伸阅读的事件
        'App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingAddEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingAddListener',
        ],
        // 词条删除词条延伸阅读的事件
        'App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingDeletedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingDeletedListener',
        ],
        // 词条添加著作延伸阅读的事件
        'App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedArticleReadingAddEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedArticleReadingAddListener',
        ],
        // 词条删除著作延伸阅读的事件
        'App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedArticleReadingDeletedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedArticleReadingDeletedListener',
        ],
        // 词条添加试卷延伸阅读的事件
        'App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedExamReadingAddEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedExamReadingAddListener',
        ],
        // 词条删除试卷延伸阅读的事件
        'App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedExamReadingDeletedEvent' => [
            'App\Listeners\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedExamReadingDeletedListener',
        ],

        // 词条协作计划创建事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationCreatedListener',
        ],
        // 词条协作计划的投票创建事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationVoteCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationVoteCreatedListener',
        ],
        // 词条协作计划投票记录的创建事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationVoteRecordCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationVoteRecordCreatedListener',
        ],
        // 词条协作计划新成员加入事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberJoinedEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationMemberJoinedListener',
        ],
        // 词条协作计划成员请退事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberFiredEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationMemberFiredListener',
        ],
        // 词条协作计划成员退出事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberQuittedEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationMemberQuittedListener',
        ],
        // 词条协作计划页面成员讨论的创建事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationDiscussionCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationDiscussionCreatedListener',
        ],
        // 词条协作计划页面成员讨论的创建事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationShutDownByManagerEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationShutDownByManagerListener',
        ],
        // 词条协作计划页面网友留言的创建事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationMessageLeftEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationMessageLeftListener',
        ],
        // 词条协作计划页面成员对网友留言的回复事件
        'App\Events\Encyclopedia\EntryCooperation\EntryCooperationMessageRepliedEvent' => [
            'App\Listeners\Encyclopedia\EntryCooperation\EntryCooperationMessageRepliedListener',
        ],

        // 词条评审计划创建事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryReview\EntryReviewCreatedListener',
        ],
        // 词条评审计划反对事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewOpponentCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryReview\EntryReviewOpponentCreatedListener',
        ],
        // 词条评审计划反对的拒绝事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewOpponentRejectedEvent' => [
            'App\Listeners\Encyclopedia\EntryReview\EntryReviewOpponentRejectedListener',
        ],
        // 词条评审计划反对的接受事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewOpponentAcceptedEvent' => [
            'App\Listeners\Encyclopedia\EntryReview\EntryReviewOpponentAcceptedListener',
        ],
        // 词条评审计划支持和中立事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewDiscussionCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryReview\EntryReviewDiscussionCreatedListener',
        ],
        // 词条评审计划评论回复事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewDiscussionRepliedEvent' => [
            'App\Listeners\Encyclopedia\EntryReview\EntryReviewDiscussionRepliedListener',
        ],
        // 词条评审计划建议事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewAdvisementCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryReview\EntryReviewAdvisementCreatedListener',
        ],
        // 词条评审计划建议的接受事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewAdvisementAcceptedEvent' => [
            'App\Listeners\Encyclopedia\EntryReview\EntryReviewAdvisementAcceptedListener',
        ],
        // 词条评审计划建议的拒绝事件
        'App\Events\Encyclopedia\EntryReview\EntryReviewAdvisementRejectedEvent' => ['App\Listeners\Encyclopedia\EntryReview\EntryReviewAdvisementRejectedListener',],
        // 评审计划的结束
        'App\Events\Encyclopedia\EntryReview\EntryReviewTerminatedEvent' => ['App\Listeners\Encyclopedia\EntryReview\EntryReviewTerminatedListener',],

        // 词条求助的创建事件
        'App\Events\Encyclopedia\EntryResort\EntryResortCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryResort\EntryResortCreatedListener',
        ],
        // 词条求助的帮助的创建事件
        'App\Events\Encyclopedia\EntryResort\EntryResortSupportCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryResort\EntryResortSupportCreatedListener',
        ],
        // 词条求助的帮助拒绝事件
        'App\Events\Encyclopedia\EntryResort\EntryResortSupportRejectedEvent' => [
            'App\Listeners\Encyclopedia\EntryResort\EntryResortSupportRejectedListener',
        ],
        // 词条求助的帮助接受事件
        'App\Events\Encyclopedia\EntryResort\EntryResortSupportAcceptedEvent' => [
            'App\Listeners\Encyclopedia\EntryResort\EntryResortSupportAcceptedListener',
        ],
        // 词条求助的帮助评论事件
        'App\Events\Encyclopedia\EntryResort\EntryResortSupportCommentCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryResort\EntryResortSupportCommentCreatedListener',
        ],

        // 词条讨论的创建事件
        'App\Events\Encyclopedia\EntryDiscussion\EntryDiscussionCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDiscussion\EntryDiscussionCreatedListener',
        ],
        // 词条普通讨论的回复事件
        'App\Events\Encyclopedia\EntryDiscussion\EntryDiscussionRepliedEvent' => [
            'App\Listeners\Encyclopedia\EntryDiscussion\EntryDiscussionRepliedListener',
        ],
        // 词条反对立场讨论的创建事件
        'App\Events\Encyclopedia\EntryDiscussion\EntryOpponentCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDiscussion\EntryOpponentCreatedListener',
        ],
        // 词条反对立场讨论的接受事件
        'App\Events\Encyclopedia\EntryDiscussion\EntryOpponentAcceptedEvent' => [
            'App\Listeners\Encyclopedia\EntryDiscussion\EntryOpponentAcceptedListener',
        ],
        // 词条反对立场讨论的拒绝事件
        'App\Events\Encyclopedia\EntryDiscussion\EntryOpponentRejectedEvent' => [
            'App\Listeners\Encyclopedia\EntryDiscussion\EntryOpponentRejectedListener',
        ],
        // 词条建议立场讨论的创建事件
        'App\Events\Encyclopedia\EntryDiscussion\EntryAdvisementCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDiscussion\EntryAdvisementCreatedListener',
        ],
        // 词条建议立场讨论的接受事件
        'App\Events\Encyclopedia\EntryDiscussion\EntryAdvisementAcceptedEvent' => [
            'App\Listeners\Encyclopedia\EntryDiscussion\EntryAdvisementAcceptedListener',
        ],
        // 词条建议立场讨论的拒绝事件
        'App\Events\Encyclopedia\EntryDiscussion\EntryAdvisementRejectedEvent' => [
            'App\Listeners\Encyclopedia\EntryDiscussion\EntryAdvisementRejectedListener',
        ],

        // 词条辩论的创建事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateCreatedListener',
        ],
        // 词条辩论辩方开篇的创建事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateBOpeningCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateBOpeningCreatedListener',
        ],
        // 词条辩论攻方自由辩论的创建事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateAFDCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateAFDCreatedListener',
        ],
        // 词条辩论辩方自由辩论的创建事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateBFDCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateBFDCreatedListener',
        ],
        // 词条辩论攻方总结陈词的创建事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateAClosingStatementCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateAClosingStatementCreatedListener',
        ],
        // 词条辩论辩方总结陈词的创建事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateBClosingStatementCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateBClosingStatementCreatedListener',
        ],
        // 词条辩论网友留言的创建事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateComment\EntryDebateCommentCreatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateComment\EntryDebateCommentCreatedListener',
        ],
        // 词条辩论的放弃事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateClosed\EntryDebateGivenUpEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateClosed\EntryDebateGivenUpListener',
        ],
        // 词条辩论的点赞事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateGiveLike\EntryDebateGivenLikeEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateGiveLike\EntryDebateGivenLikeListener',
        ],
        // 词条辩论裁判的加入事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateRefereeJoinedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateRefereeJoinedListener',
        ],
        // 词条辩论裁判分析的更新事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateAnalyseUpdatedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateAnalyseUpdatedListener',
        ],
        // 词条辩论裁判总结事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateSummarySubmittedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateSummarySubmittedListener',
        ],
        // 词条辩论由于裁判超时的自动结算事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateClear\EntryDebateTimeOutByRefereeClearedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateClear\EntryDebateTimeOutByRefereeClearedListener',
        ],
        // 词条辩论自动结算事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateClear\EntryDebateAutomaticallyClearedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateClear\EntryDebateAutomaticallyClearedListener',
        ],
        // 词条辩论的超时关闭事件
        'App\Events\Encyclopedia\EntryDebate\EntryDebateClosed\EntryDebateTimeOutClosedEvent' => [
            'App\Listeners\Encyclopedia\EntryDebate\EntryDebateClosed\EntryDebateTimeOutClosedListener',
        ],

        // 新的著作创建触发事件**********************************************************************************
        'App\Events\Publication\ArticleCreatedEvent' => [
            'App\Listeners\Publication\ArticleCreatedListener',
        ],
        // 著作热度增加事件
        'App\Events\Publication\Recommend\ArticleTemperatureRecordAddedEvent' => [
            'App\Listeners\Publication\Recommend\ArticleTemperatureRecordAddedListener',
        ],
        // 著作热度更新事件
        'App\Events\Publication\Recommend\ArticleTemperatureUpdatedEvent' => [
            'App\Listeners\Publication\Recommend\ArticleTemperatureUpdatedListener',
        ],
        // 著作推荐更新事件
        'App\Events\Publication\Recommend\ArticleRecommendationUpdatedEvent' => [
            'App\Listeners\Publication\Recommend\ArticleRecommendationUpdatedListener',
        ],
        // 浏览量更新
        'App\Events\Publication\ArticleViewsUpdatedEvent' => [
            'App\Listeners\Publication\ArticleViewsUpdatedListener',
        ],
        // 词条关注
        'App\Events\Publication\Article\Focus\ArticleFocusedEvent' => [
            'App\Listeners\Publication\Article\Focus\ArticleFocusedListener',
        ],
        // 词条取消关注
        'App\Events\Publication\Article\Focus\ArticleFocusCanceledEvent' => [
            'App\Listeners\Publication\Article\Focus\ArticleFocusCanceledListener',
        ],
        // 词条收藏
        'App\Events\Publication\Article\Focus\ArticleCollectedEvent' => [
            'App\Listeners\Publication\Article\Focus\ArticleCollectedListener',
        ],
        // 词条取消收藏
        'App\Events\Publication\Article\Focus\ArticleCollectCanceledEvent' => [
            'App\Listeners\Publication\Article\Focus\ArticleCollectCanceledListener',
        ],
        // 不再触发新的著作第一次编辑正文触发事件，改到著作正文内容创建触发事件（著作内容是分章的，所以每章创建都会触发事件）
        'App\Events\Publication\Article\ArticleContentCreatedEvent' => [
            'App\Listeners\Publication\Article\ArticleContentCreatedListener',
        ],
        // 著作协作计划创建事件
        'App\Events\Publication\ArticleCooperationCreatedEvent' => [
            'App\Listeners\Publication\ArticleCooperationCreatedListener',
        ],
        // 著作修改摘要触发事件
        'App\Events\Publication\ArticleSummaryModifiedEvent' => [
            'App\Listeners\Publication\ArticleSummaryModifiedListener',
        ],
        // 著作修改了关键词的事件
        'App\Events\Publication\Article\ArticleKeywordModifiedEvent' => [
            'App\Listeners\Publication\Article\ArticleKeywordModifiedListener',
        ],
        // 著作章节内容编辑的事件
        'App\Events\Publication\Article\ArticleContentModifiedEvent' => [
            'App\Listeners\Publication\Article\ArticleContentModifiedListener',
        ],
        // 著作章节内容删除的事件
        'App\Events\Publication\Article\ArticleContentDeletedEvent' => [
            'App\Listeners\Publication\Article\ArticleContentDeletedListener',
        ],
        // 著作内容新增参考文献的事件
        'App\Events\Publication\Article\ArticleReference\ArticleReferenceAddEvent' => [
            'App\Listeners\Publication\Article\ArticleReference\ArticleReferenceAddListener',
        ],
        // 著作内容修改参考文献的事件
        'App\Events\Publication\Article\ArticleReference\ArticleReferenceModifiedEvent' => [
            'App\Listeners\Publication\Article\ArticleReference\ArticleReferenceModifiedListener',
        ],
        // 著作内容删除参考文献的事件，著作内容的删除不会触发该事件
        'App\Events\Publication\Article\ArticleReference\ArticleReferenceDeletedEvent' => [
            'App\Listeners\Publication\Article\ArticleReference\ArticleReferenceDeletedListener',
        ],
        // 著作添加向百科延伸阅读的事件
        'App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedReadingAddEvent' => [
            'App\Listeners\Publication\Article\ArticleExtendedReading\ArticleExtendedReadingAddListener',
        ],
        // 著作删除向百科延伸阅读的事件
        'App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedReadingDeletedEvent' => [
            'App\Listeners\Publication\Article\ArticleExtendedReading\ArticleExtendedReadingDeletedListener',
        ],
        // 著作添加向著作延伸阅读的事件
        'App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedArticleReadingAddEvent' => [
            'App\Listeners\Publication\Article\ArticleExtendedReading\ArticleExtendedArticleReadingAddListener',
        ],
        // 著作删除向著作延伸阅读的事件
        'App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedArticleReadingDeletedEvent' => [
            'App\Listeners\Publication\Article\ArticleExtendedReading\ArticleExtendedArticleReadingDeletedListener',
        ],
        // 著作添加向著作延伸阅读的事件
        'App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedExamReadingAddEvent' => [
            'App\Listeners\Publication\Article\ArticleExtendedReading\ArticleExtendedExamReadingAddListener',
        ],
        // 著作删除向著作延伸阅读的事件
        'App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedExamReadingDeletedEvent' => [
            'App\Listeners\Publication\Article\ArticleExtendedReading\ArticleExtendedExamReadingDeletedListener',
        ],

        // 著作协作计划更改分配任务的事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationAssignModifiedEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationAssignModifiedListener',
        ],
        // 著作协作计划页面成员发表讨论的事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationDiscussionCreatedEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationDiscussionCreatedListener',
        ],
        // 著作协作计划页面网友留言的创建事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationMessageLeftEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationMessageLeftListener',
        ],
        // 著作协作计划页面成员对网友留言的回复事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationMessageRepliedEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationMessageRepliedListener',
        ],
        // 著作协作计划的投票创建事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationVoteCreatedEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationVoteCreatedListener',
        ],
        // 著作协作计划投票记录的创建事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationVoteRecordCreatedEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationVoteRecordCreatedListener',
        ],
        // 著作协作计划新成员加入事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationMemberJoinedEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationMemberJoinedListener',
        ],
        // 著作协作计划成员请退事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationMemberFiredEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationMemberFiredListener',
        ],
        // 著作协作计划成员退出事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationMemberQuittedEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationMemberQuittedListener',
        ],
        // 著作协作计划关闭事件
        'App\Events\Publication\ArticleCooperation\ArticleCooperationShutDownByManagerEvent' => [
            'App\Listeners\Publication\ArticleCooperation\ArticleCooperationShutDownByManagerListener',
        ],

        // 著作评审计划创建事件*****************************************************
        'App\Events\Publication\ArticleReview\ArticleReviewCreatedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewCreatedListener',
        ],
        // 著作评审计划反对事件
        'App\Events\Publication\ArticleReview\ArticleReviewOpponentCreatedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewOpponentCreatedListener',
        ],
        // 著作评审计划反对的拒绝事件
        'App\Events\Publication\ArticleReview\ArticleReviewOpponentRejectedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewOpponentRejectedListener',
        ],
        // 著作评审计划反对的接受事件
        'App\Events\Publication\ArticleReview\ArticleReviewOpponentAcceptedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewOpponentAcceptedListener',
        ],
        // 著作评审计划支持和中立事件
        'App\Events\Publication\ArticleReview\ArticleReviewDiscussionCreatedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewDiscussionCreatedListener',
        ],
        // 著作评审计划评论回复事件
        'App\Events\Publication\ArticleReview\ArticleReviewDiscussionRepliedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewDiscussionRepliedListener',
        ],
        // 著作评审计划建议事件
        'App\Events\Publication\ArticleReview\ArticleReviewAdvisementCreatedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewAdvisementCreatedListener',
        ],
        // 著作评审计划建议的接受事件
        'App\Events\Publication\ArticleReview\ArticleReviewAdvisementAcceptedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewAdvisementAcceptedListener',
        ],
        // 著作评审计划建议的拒绝事件
        'App\Events\Publication\ArticleReview\ArticleReviewAdvisementRejectedEvent' => [
            'App\Listeners\Publication\ArticleReview\ArticleReviewAdvisementRejectedListener',
        ],
        // 评审计划的结束
        'App\Events\Publication\ArticleReview\ArticleReviewTerminatedEvent' => ['App\Listeners\Publication\ArticleReview\ArticleReviewTerminatedListener',],

        // 著作求助的创建事件********
        'App\Events\Publication\ArticleResort\ArticleResortCreatedEvent' => [
            'App\Listeners\Publication\ArticleResort\ArticleResortCreatedListener',
        ],
        // 著作求助的帮助的创建事件
        'App\Events\Publication\ArticleResort\ArticleResortSupportCreatedEvent' => [
            'App\Listeners\Publication\ArticleResort\ArticleResortSupportCreatedListener',
        ],
        // 著作求助的帮助拒绝事件
        'App\Events\Publication\ArticleResort\ArticleResortSupportRejectedEvent' => [
            'App\Listeners\Publication\ArticleResort\ArticleResortSupportRejectedListener',
        ],
        // 著作求助的帮助接受事件
        'App\Events\Publication\ArticleResort\ArticleResortSupportAcceptedEvent' => [
            'App\Listeners\Publication\ArticleResort\ArticleResortSupportAcceptedListener',
        ],
        // 著作求助的帮助评论事件
        'App\Events\Publication\ArticleResort\ArticleResortSupportCommentCreatedEvent' => [
            'App\Listeners\Publication\ArticleResort\ArticleResortSupportCommentCreatedListener',
        ],

        // 著作的讨论********************************
        // 著作讨论的创建事件
        'App\Events\Publication\ArticleDiscussion\ArticleDiscussionCreatedEvent' => [
            'App\Listeners\Publication\ArticleDiscussion\ArticleDiscussionCreatedListener',
        ],
        // 著作普通讨论的回复事件
        'App\Events\Publication\ArticleDiscussion\ArticleDiscussionRepliedEvent' => [
            'App\Listeners\Publication\ArticleDiscussion\ArticleDiscussionRepliedListener',
        ],
        // 著作反对立场讨论的创建事件
        'App\Events\Publication\ArticleDiscussion\ArticleOpponentCreatedEvent' => [
            'App\Listeners\Publication\ArticleDiscussion\ArticleOpponentCreatedListener',
        ],
        // 著作反对立场讨论的接受事件
        'App\Events\Publication\ArticleDiscussion\ArticleOpponentAcceptedEvent' => [
            'App\Listeners\Publication\ArticleDiscussion\ArticleOpponentAcceptedListener',
        ],
        // 著作反对立场讨论的拒绝事件
        'App\Events\Publication\ArticleDiscussion\ArticleOpponentRejectedEvent' => [
            'App\Listeners\Publication\ArticleDiscussion\ArticleOpponentRejectedListener',
        ],
        // 著作建议立场讨论的创建事件
        'App\Events\Publication\ArticleDiscussion\ArticleAdvisementCreatedEvent' => [
            'App\Listeners\Publication\ArticleDiscussion\ArticleAdvisementCreatedListener',
        ],
        // 著作建议立场讨论的接受事件
        'App\Events\Publication\ArticleDiscussion\ArticleAdvisementAcceptedEvent' => [
            'App\Listeners\Publication\ArticleDiscussion\ArticleAdvisementAcceptedListener',
        ],
        // 著作建议立场讨论的拒绝事件
        'App\Events\Publication\ArticleDiscussion\ArticleAdvisementRejectedEvent' => [
            'App\Listeners\Publication\ArticleDiscussion\ArticleAdvisementRejectedListener',
        ],

        // 著作辩论的创建事件*******************************************************
        'App\Events\Publication\ArticleDebate\ArticleDebateCreatedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateCreatedListener',
        ],
        // 著作辩论辩方开篇的创建事件
        'App\Events\Publication\ArticleDebate\ArticleDebateBOpeningCreatedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateBOpeningCreatedListener',
        ],
        // 著作辩论攻方自由辩论的创建事件
        'App\Events\Publication\ArticleDebate\ArticleDebateAFDCreatedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateAFDCreatedListener',
        ],
        // 著作辩论辩方自由辩论的创建事件
        'App\Events\Publication\ArticleDebate\ArticleDebateBFDCreatedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateBFDCreatedListener',
        ],
        // 著作辩论攻方总结陈词的创建事件
        'App\Events\Publication\ArticleDebate\ArticleDebateAClosingStatementCreatedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateAClosingStatementCreatedListener',
        ],
        // 著作辩论辩方总结陈词的创建事件
        'App\Events\Publication\ArticleDebate\ArticleDebateBClosingStatementCreatedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateBClosingStatementCreatedListener',
        ],
        // 著作辩论的放弃事件
        'App\Events\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateGivenUpEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateGivenUpListener',
        ],
        // 著作辩论的点赞事件
        'App\Events\Publication\ArticleDebate\ArticleDebateGiveLike\ArticleDebateGivenLikeEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateGiveLike\ArticleDebateGivenLikeListener',
        ],
        // 著作辩论裁判的加入事件
        'App\Events\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateRefereeJoinedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateRefereeJoinedListener',
        ],
        // 著作辩论裁判分析的更新事件
        'App\Events\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateAnalyseUpdatedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateAnalyseUpdatedListener',
        ],
        // 著作辩论裁判总结事件
        'App\Events\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateSummarySubmittedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateSummarySubmittedListener',
        ],
        // 著作辩论由于裁判超时的自动结算事件
        'App\Events\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateTimeOutByRefereeClearedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateTimeOutByRefereeClearedListener',
        ],
        // 著作辩论自动结算事件
        'App\Events\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateAutomaticallyClearedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateAutomaticallyClearedListener',
        ],
        // 著作辩论的超时关闭事件
        'App\Events\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateTimeOutClosedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateTimeOutClosedListener',
        ],
        // 著作辩论网友留言的创建事件
        'App\Events\Publication\ArticleDebate\ArticleDebateComment\ArticleDebateCommentCreatedEvent' => [
            'App\Listeners\Publication\ArticleDebate\ArticleDebateComment\ArticleDebateCommentCreatedListener',
        ],

        // 试卷版块开始**************************************************************************************************
        // 新建试卷事件
        'App\Events\Examination\ExamCreatedEvent' => [
            'App\Listeners\Examination\ExamCreatedListener',
        ],
        // 试卷热度增加事件
        'App\Events\Examination\Recommend\ExamTemperatureRecordAddedEvent' => [
            'App\Listeners\Examination\Recommend\ExamTemperatureRecordAddedListener',
        ],

        // 试卷热度更新事件
        'App\Events\Examination\Recommend\ExamTemperatureUpdatedEvent' => [
            'App\Listeners\Examination\Recommend\ExamTemperatureUpdatedListener',
        ],
        // 试卷浏览量更新
        'App\Events\Examination\ExamViewsUpdatedEvent' => [
            'App\Listeners\Examination\ExamViewsUpdatedListener',
        ],

        // 试卷推荐更新事件
        'App\Events\Examination\Recommend\ExamRecommendationUpdatedEvent' => [
            'App\Listeners\Examination\Recommend\ExamRecommendationUpdatedListener',
        ],
        // 试卷关注
        'App\Events\Examination\Exam\Focus\ExamFocusedEvent' => [
            'App\Listeners\Examination\Exam\Focus\ExamFocusedListener',
        ],
        // 试卷取消关注
        'App\Events\Examination\Exam\Focus\ExamFocusCanceledEvent' => [
            'App\Listeners\Examination\Exam\Focus\ExamFocusCanceledListener',
        ],
        // 试卷收藏
        'App\Events\Examination\Exam\Focus\ExamCollectedEvent' => [
            'App\Listeners\Examination\Exam\Focus\ExamCollectedListener',
        ],
        // 试卷取消收藏
        'App\Events\Examination\Exam\Focus\ExamCollectCanceledEvent' => [
            'App\Listeners\Examination\Exam\Focus\ExamCollectCanceledListener',
        ],
        // 试卷的协作计划创建事件
        'App\Events\Examination\ExamCooperationCreatedEvent' => [
            'App\Listeners\Examination\ExamCooperationCreatedListener',
        ],
        // 只要有创建新的题目就触发该事件
        'App\Events\Examination\Exam\ExamQuestionCreatedEvent' => [
            'App\Listeners\Examination\Exam\ExamQuestionCreatedListener',
        ],
        // 修改摘要触发事件
        'App\Events\Examination\ExamSummaryModifiedEvent' => [
            'App\Listeners\Examination\ExamSummaryModifiedListener',
        ],
        // 创建材料
        'App\Events\Examination\PartStem\ExamPartStemCreatedEvent' => [
            'App\Listeners\Examination\PartStem\ExamPartStemCreatedListener',
        ],
        // 材料的编辑事件
        'App\Events\Examination\PartStem\ExamPartStemModifiedEvent' => [
            'App\Listeners\Examination\PartStem\ExamPartStemModifiedListener',
        ],
        // 题干的编辑事件
        'App\Events\Examination\Exam\QuestionMethod\ExamQuestionStemModifiedEvent' => [
            'App\Listeners\Examination\Exam\QuestionMethod\ExamQuestionStemModifiedListener',
        ],
        // 选项的编辑事件
        'App\Events\Examination\Exam\QuestionMethod\ExamQuestionOptionModifiedEvent' => [
            'App\Listeners\Examination\Exam\QuestionMethod\ExamQuestionOptionModifiedListener',
        ],
        // 注释的编辑事件
        'App\Events\Examination\Exam\QuestionMethod\ExamQuestionAnnotationModifiedEvent' => [
            'App\Listeners\Examination\Exam\QuestionMethod\ExamQuestionAnnotationModifiedListener',
        ],
        // 答案的编辑事件
        'App\Events\Examination\Exam\QuestionMethod\ExamQuestionAnswerModifiedEvent' => [
            'App\Listeners\Examination\Exam\QuestionMethod\ExamQuestionAnswerModifiedListener',
        ],
        // 题目的前移事件
        'App\Events\Examination\Exam\QuestionMethod\ExamQuestionMoveForwardEvent' => [
            'App\Listeners\Examination\Exam\QuestionMethod\ExamQuestionMoveForwardListener',
        ],
        // 题目的后移事件
        'App\Events\Examination\Exam\QuestionMethod\ExamQuestionMoveBackwardEvent' => [
            'App\Listeners\Examination\Exam\QuestionMethod\ExamQuestionMoveBackwardListener',
        ],
        // 题目的删除事件
        'App\Events\Examination\Exam\QuestionMethod\ExamQuestionDeletedEvent' => [
            'App\Listeners\Examination\Exam\QuestionMethod\ExamQuestionDeletedListener',
        ],
        // 材料的前移事件
        'App\Events\Examination\Exam\PartStem\PartStemMoveForwardEvent' => [
            'App\Listeners\Examination\Exam\PartStem\PartStemMoveForwardListener',
        ],
        // 材料的后移事件
        'App\Events\Examination\Exam\PartStem\PartStemMoveBackwardEvent' => [
            'App\Listeners\Examination\Exam\PartStem\PartStemMoveBackwardListener',
        ],
        // 材料的删除事件
        'App\Events\Examination\Exam\PartStem\PartStemDeletedEvent' => [
            'App\Listeners\Examination\Exam\PartStem\PartStemDeletedListener',
        ],
        // 延伸试卷阅读添加事件.....................................................................
        'App\Events\Examination\ExtendedReading\ExamExtendedExamAddedEvent' => [
            'App\Listeners\Examination\ExtendedReading\ExamExtendedExamAddedListener',
        ],
        // 延伸阅读删除事件
        'App\Events\Examination\ExtendedReading\ExamExtendedExamDeletedEvent' => [
            'App\Listeners\Examination\ExtendedReading\ExamExtendedExamDeletedListener',
        ],
        // 延伸词条阅读添加事件
        'App\Events\Examination\ExtendedReading\ExamExtendedEntryAddedEvent' => [
            'App\Listeners\Examination\ExtendedReading\ExamExtendedEntryAddedListener',
        ],
        // 延伸阅读删除事件
        'App\Events\Examination\ExtendedReading\ExamExtendedEntryDeletedEvent' => [
            'App\Listeners\Examination\ExtendedReading\ExamExtendedEntryDeletedListener',
        ],
        // 延伸著作阅读添加事件
        'App\Events\Examination\ExtendedReading\ExamExtendedArticleAddedEvent' => [
            'App\Listeners\Examination\ExtendedReading\ExamExtendedArticleAddedListener',
        ],
        // 延伸阅读删除事件...............................................................................
        'App\Events\Examination\ExtendedReading\ExamExtendedArticleDeletedEvent' => [
            'App\Listeners\Examination\ExtendedReading\ExamExtendedArticleDeletedListener',
        ],
        // 考试记录添加
        'App\Events\Examination\ExamReport\ExamRecordAddEvent' => ['App\Listeners\Examination\ExamReport\ExamRecordAddListener',],
        // 试卷协作计划更改分配任务的事件
        'App\Events\Examination\ExamCooperation\ExamCooperationAssignModifiedEvent' => [
            'App\Listeners\Examination\ExamCooperation\ExamCooperationAssignModifiedListener',
        ],
        // 试卷协作计划页面成员发表讨论的事件
        'App\Events\Examination\ExamCooperation\Discussion\DiscussionCreatedEvent' => [
            'App\Listeners\Examination\ExamCooperation\Discussion\DiscussionCreatedListener',
        ],
        // 试卷协作计划页面网友留言的创建事件
        'App\Events\Examination\ExamCooperation\Discussion\MessageLeftEvent' => [
            'App\Listeners\Examination\ExamCooperation\Discussion\MessageLeftListener',
        ],
        // 试卷协作计划页面成员对网友留言的回复事件
        'App\Events\Examination\ExamCooperation\Discussion\MessageRepliedEvent' => [
            'App\Listeners\Examination\ExamCooperation\Discussion\MessageRepliedListener',
        ],
        // 试卷协作计划的投票创建事件
        'App\Events\Examination\ExamCooperation\Vote\VoteCreatedEvent' => [
            'App\Listeners\Examination\ExamCooperation\Vote\VoteCreatedListener',
        ],
        // 试卷协作计划投票记录的创建事件
        'App\Events\Examination\ExamCooperation\Vote\VoteRecordCreatedEvent' => [
            'App\Listeners\Examination\ExamCooperation\Vote\VoteRecordCreatedListener',
        ],
        // 试卷协作计划新成员加入事件
        'App\Events\Examination\ExamCooperation\Member\MemberJoinedEvent' => [
            'App\Listeners\Examination\ExamCooperation\Member\MemberJoinedListener',
        ],
        // 试卷协作计划成员请退事件
        'App\Events\Examination\ExamCooperation\Member\MemberFiredEvent' => [
            'App\Listeners\Examination\ExamCooperation\Member\MemberFiredListener',
        ],
        // 试卷协作计划成员退出事件
        'App\Events\Examination\ExamCooperation\Member\MemberQuittedEvent' => [
            'App\Listeners\Examination\ExamCooperation\Member\MemberQuittedListener',
        ],
        // 著作协作计划关闭事件
        'App\Events\Examination\ExamCooperation\ExamCooperationShutDownByManagerEvent' => [
            'App\Listeners\Examination\ExamCooperation\ExamCooperationShutDownByManagerListener',
        ],

        // 试卷评审计划创建事件*****************************************************
        'App\Events\Examination\ExamReview\ExamReviewCreatedEvent' => [
            'App\Listeners\Examination\ExamReview\ExamReviewCreatedListener',
        ],
        // 试卷评审计划反对事件
        'App\Events\Examination\ExamReview\ExamReviewOpponentCreatedEvent' => [
            'App\Listeners\Examination\ExamReview\ExamReviewOpponentCreatedListener',
        ],
        // 试卷评审计划反对的拒绝事件
        'App\Events\Examination\ExamReview\ExamReviewOpponentRejectedEvent' => [
            'App\Listeners\Examination\ExamReview\ExamReviewOpponentRejectedListener',
        ],
        // 试卷评审计划反对的接受事件
        'App\Events\Examination\ExamReview\ExamReviewOpponentAcceptedEvent' => [
            'App\Listeners\Examination\ExamReview\ExamReviewOpponentAcceptedListener',
        ],
        // 试卷评审计划支持和中立事件
        'App\Events\Examination\ExamReview\ExamReviewDiscussionCreatedEvent' => [
            'App\Listeners\Examination\ExamReview\ExamReviewDiscussionCreatedListener',
        ],
        // 试卷评审计划评论回复事件
        'App\Events\Examination\ExamReview\ExamReviewDiscussionRepliedEvent' => [
            'App\Listeners\Examination\ExamReview\ExamReviewDiscussionRepliedListener',
        ],
        // 试卷评审计划建议事件
        'App\Events\Examination\ExamReview\ExamReviewAdvisementCreatedEvent' => [
            'App\Listeners\Examination\ExamReview\ExamReviewAdvisementCreatedListener',
        ],
        // 试卷评审计划建议的接受事件
        'App\Events\Examination\ExamReview\ExamReviewAdvisementAcceptedEvent' => [
            'App\Listeners\Examination\ExamReview\ExamReviewAdvisementAcceptedListener',
        ],
        // 试卷评审计划建议的拒绝事件
        'App\Events\Examination\ExamReview\ExamReviewAdvisementRejectedEvent' => ['App\Listeners\Examination\ExamReview\ExamReviewAdvisementRejectedListener',],
        // 评审计划的结束
        'App\Events\Examination\ExamReview\ExamReviewTerminatedEvent' => ['App\Listeners\Examination\ExamReview\ExamReviewTerminatedListener',],
        // 试卷求助的创建事件********
        'App\Events\Examination\ExamResort\ExamResortCreatedEvent' => [
            'App\Listeners\Examination\ExamResort\ExamResortCreatedListener',
        ],
        // 试卷求助的帮助的创建事件
        'App\Events\Examination\ExamResort\ExamResortSupportCreatedEvent' => [
            'App\Listeners\Examination\ExamResort\ExamResortSupportCreatedListener',
        ],
        // 试卷求助的帮助拒绝事件
        'App\Events\Examination\ExamResort\ExamResortSupportRejectedEvent' => [
            'App\Listeners\Examination\ExamResort\ExamResortSupportRejectedListener',
        ],
        // 试卷求助的帮助接受事件
        'App\Events\Examination\ExamResort\ExamResortSupportAcceptedEvent' => [
            'App\Listeners\Examination\ExamResort\ExamResortSupportAcceptedListener',
        ],
        // 试卷求助的帮助评论事件
        'App\Events\Examination\ExamResort\ExamResortSupportCommentCreatedEvent' => [
            'App\Listeners\Examination\ExamResort\ExamResortSupportCommentCreatedListener',
        ],

        // 试卷的讨论********************************
        // 试卷讨论的创建事件
        'App\Events\Examination\ExamDiscussion\ExamDiscussionCreatedEvent' => [
            'App\Listeners\Examination\ExamDiscussion\ExamDiscussionCreatedListener',
        ],
        // 试卷普通讨论的回复事件
        'App\Events\Examination\ExamDiscussion\ExamDiscussionRepliedEvent' => [
            'App\Listeners\Examination\ExamDiscussion\ExamDiscussionRepliedListener',
        ],
        // 试卷反对立场讨论的创建事件
        'App\Events\Examination\ExamDiscussion\ExamOpponentCreatedEvent' => [
            'App\Listeners\Examination\ExamDiscussion\ExamOpponentCreatedListener',
        ],
        // 试卷反对立场讨论的接受事件
        'App\Events\Examination\ExamDiscussion\ExamOpponentAcceptedEvent' => [
            'App\Listeners\Examination\ExamDiscussion\ExamOpponentAcceptedListener',
        ],
        // 试卷反对立场讨论的拒绝事件
        'App\Events\Examination\ExamDiscussion\ExamOpponentRejectedEvent' => [
            'App\Listeners\Examination\ExamDiscussion\ExamOpponentRejectedListener',
        ],
        // 试卷建议立场讨论的创建事件
        'App\Events\Examination\ExamDiscussion\ExamAdvisementCreatedEvent' => [
            'App\Listeners\Examination\ExamDiscussion\ExamAdvisementCreatedListener',
        ],
        // 试卷建议立场讨论的接受事件
        'App\Events\Examination\ExamDiscussion\ExamAdvisementAcceptedEvent' => [
            'App\Listeners\Examination\ExamDiscussion\ExamAdvisementAcceptedListener',
        ],
        // 试卷建议立场讨论的拒绝事件
        'App\Events\Examination\ExamDiscussion\ExamAdvisementRejectedEvent' => [
            'App\Listeners\Examination\ExamDiscussion\ExamAdvisementRejectedListener',
        ],

        // 试卷辩论的创建事件*******************************************************
        'App\Events\Examination\ExamDebate\ExamDebateCreatedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateCreatedListener',
        ],
        // 试卷辩论辩方开篇的创建事件
        'App\Events\Examination\ExamDebate\ExamDebateBOpeningCreatedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateBOpeningCreatedListener',
        ],
        // 试卷辩论攻方自由辩论的创建事件
        'App\Events\Examination\ExamDebate\ExamDebateAFDCreatedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateAFDCreatedListener',
        ],
        // 试卷辩论辩方自由辩论的创建事件
        'App\Events\Examination\ExamDebate\ExamDebateBFDCreatedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateBFDCreatedListener',
        ],
        // 试卷辩论攻方总结陈词的创建事件
        'App\Events\Examination\ExamDebate\ExamDebateAClosingStatementCreatedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateAClosingStatementCreatedListener',
        ],
        // 试卷辩论辩方总结陈词的创建事件
        'App\Events\Examination\ExamDebate\ExamDebateBClosingStatementCreatedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateBClosingStatementCreatedListener',
        ],
        // 试卷辩论的放弃事件
        'App\Events\Examination\ExamDebate\ExamDebateClosed\ExamDebateGivenUpEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateClosed\ExamDebateGivenUpListener',
        ],
        // 试卷辩论的点赞事件
        'App\Events\Examination\ExamDebate\ExamDebateGiveLike\ExamDebateGivenLikeEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateGiveLike\ExamDebateGivenLikeListener',
        ],
        // 试卷辩论裁判的加入事件
        'App\Events\Examination\ExamDebate\ExamDebateReferee\ExamDebateRefereeJoinedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateReferee\ExamDebateRefereeJoinedListener',
        ],
        // 试卷辩论裁判分析的更新事件
        'App\Events\Examination\ExamDebate\ExamDebateReferee\ExamDebateAnalyseUpdatedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateReferee\ExamDebateAnalyseUpdatedListener',
        ],
        // 试卷辩论裁判总结事件
        'App\Events\Examination\ExamDebate\ExamDebateReferee\ExamDebateSummarySubmittedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateReferee\ExamDebateSummarySubmittedListener',
        ],
        // 试卷辩论由于裁判超时的自动结算事件
        'App\Events\Examination\ExamDebate\ExamDebateClear\ExamDebateTimeOutByRefereeClearedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateClear\ExamDebateTimeOutByRefereeClearedListener',
        ],
        // 试卷辩论自动结算事件
        'App\Events\Examination\ExamDebate\ExamDebateClear\ExamDebateAutomaticallyClearedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateClear\ExamDebateAutomaticallyClearedListener',
        ],
        // 试卷辩论的超时关闭事件
        'App\Events\Examination\ExamDebate\ExamDebateClosed\ExamDebateTimeOutClosedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateClosed\ExamDebateTimeOutClosedListener',
        ],
        // 试卷辩论网友留言的创建事件
        'App\Events\Examination\ExamDebate\ExamDebateComment\ExamDebateCommentCreatedEvent' => [
            'App\Listeners\Examination\ExamDebate\ExamDebateComment\ExamDebateCommentCreatedListener',
        ],

        // 组织版块开始**************************************************************************************************
        // 新建组织事件
        'App\Events\Organization\GroupCreatedEvent' => [
            'App\Listeners\Organization\GroupCreatedListener',
        ],
        // 组织介绍修改事件
        'App\Events\Organization\Group\GroupIntroModifiedEvent' => [
            'App\Listeners\Organization\Group\GroupIntroModifiedListener',
        ],
        // 组织徽章修改事件
        'App\Events\Organization\Group\GroupEmblemModifiedEvent' => [
            'App\Listeners\Organization\Group\GroupEmblemModifiedListener',
        ],
        // 组织的投票创建事件
        'App\Events\Organization\Group\Vote\VoteCreatedEvent' => [
            'App\Listeners\Organization\Group\Vote\VoteCreatedListener',
        ],
        // 组织投票记录的创建事件
        'App\Events\Organization\Group\Vote\VoteRecordCreatedEvent' => [
            'App\Listeners\Organization\Group\Vote\VoteRecordCreatedListener',
        ],
        // 组织成员加入事件
        'App\Events\Organization\Group\Member\GroupMemberJoinedEvent' => [
            'App\Listeners\Organization\Group\Member\GroupMemberJoinedListener',
        ],
        // 组织成员请退事件
        'App\Events\Organization\Group\Member\GroupMemberFiredEvent' => [
            'App\Listeners\Organization\Group\Member\GroupMemberFiredListener',
        ],
        // 组织成员退出事件
        'App\Events\Organization\Group\Member\GroupMemberQuittedEvent' => [
            'App\Listeners\Organization\Group\Member\GroupMemberQuittedListener',
        ],
        // 组织成员变更位置事件
        'App\Events\Organization\Group\Member\GroupMemberPositionChangedEvent' => [
            'App\Listeners\Organization\Group\Member\GroupMemberPositionChangedListener',
        ],
        // 组织文档创建事件
        'App\Events\Organization\Group\GroupDoc\GroupDocCreatedEvent' => [
            'App\Listeners\Organization\Group\GroupDoc\GroupDocCreatedListener',
        ],
        // 组织文档评论创建事件
        'App\Events\Organization\Group\GroupDoc\GroupDocCommentCreatedEvent' => [
            'App\Listeners\Organization\Group\GroupDoc\GroupDocCommentCreatedListener',
        ],
        // 组织文档评论回复事件
        'App\Events\Organization\Group\GroupDoc\GroupDocCommentRepliedEvent' => [
            'App\Listeners\Organization\Group\GroupDoc\GroupDocCommentRepliedListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
