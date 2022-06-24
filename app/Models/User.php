<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Events\Personal\Credit\UserCreditChangedEvent;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject,MustVerifyEmail
{
    use HasApiTokens,HasFactory,Notifiable;


    //定义关联的数据表
    protected $table = 'users';
    public $timestamps = true;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'signature','email_verified_at','level','gold','silver','copper','role_id','status','exp_value','grow_value','committee_id','gid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    //多对多关联user表，取得关注用户数据
    public function getFocusUsers(){
        return $this->belongsToMany('App\Models\User','user_focus_relationships','user_id','focus_id');
    }

    //远程关联道具表
    public function getProp(){
        return $this->hasManyThrough('App\Home\Personnel\Prop','App\Home\Personnel\Prop\UserProp');
    }

    //一对一图片表，得到头像地址
    public function getAvatar(){
        return $this->hasOne('App\Home\Personal\UserPicture','id','avatar');
    }

    //多对多关联专业分类表，取得兴趣专业
    public function getInterest(){
        return $this->belongsToMany('App\Home\Classification','user_classes','user_id','class_id');
    }

    //一对一关联用户角色表，获取角色
    public function getRole(){
        return $this->hasOne('App\Home\Personnel\Role','id','role_id');
    }

    //一对一关联用户等级表，获取等级
    public function getLevel(){
        return $this->hasOne('App\Home\Personnel\Level','id','level');
    }

    //多对多关联里程碑表，取得里程碑
    public function getMilestones(){
        return $this->belongsToMany('App\Home\Personnel\Milestone','user_milestones','user_id','milestone_id');
    }

    //一对多关联用户道具表，取得道具id
    public function getProps(){
        return $this->hasMany('App\Home\Personnel\Prop\UserProp','user_id','id');
    }

    //一对多关联取得我的自管理词条表
    public function getMyManageEntries(){
        return $this->hasMany('App\Home\Encyclopedia\Entry','manage_id','id');
    }


    //一对多关联取得我的自管理词条求助表（要筛选pid=0的情况）
    public function getMyManageEntryResorts(){
        return $this->hasMany('App\Home\Encyclopedia\EntryResort','author_id','id');
    }

    //一对多关联取得我的自管理词条评审表
    public function getMyManageEntryReviews(){
        return $this->hasMany('App\Home\Encyclopedia\EntryReview','initiate_id','id');
    }

    //一对多关联取得我的自管理词条攻辩表A
    public function getMyAEntryDebates(){
        return $this->hasMany('App\Home\Encyclopedia\EntryDebate','Aauthor_id','id');
    }

    //一对多关联取得我的自管理词条攻辩表B
    public function getMyBEntryDebates(){
        return $this->hasMany('App\Home\Encyclopedia\EntryDebate','Bauthor_id','id');
    }

    //一对多关联取得我的自管理词条攻辩表R
    public function getMyREntryDebates(){
        return $this->hasMany('App\Home\Encyclopedia\EntryDebate','referee_id','id');
    }

    //一对多关联取得我的词条反对表（要筛选pid=0的情况）
    public function getMyEntryOpponents(){
        return $this->hasMany('App\Home\Encyclopedia\EntryDiscussion\EntryOpponent','author_id','id');
    }

    //一对多关联取得我的词条建议表（要筛选pid=0的情况）
    public function getMyEntryAdvises(){
        return $this->hasMany('App\Home\Encyclopedia\EntryDiscussion\EntryAdvise','author_id','id');
    }

    //一对多关联取得我的自管理著作表
    public function getMyManageArticles(){
        return $this->hasMany('App\Home\Publication\Article','manage_id','id');
    }

    //一对多关联取得我的自管理著作协作计划表
    public function getMyManageArticleCooperations(){
        return $this->hasMany('App\Home\Publication\ArticleCooperation','manage_id','id');
    }

    //一对多关联取得我的自管理著作求助表（要筛选pid=0的情况）
    public function getMyManageArticleResorts(){
        return $this->hasMany('App\Home\Publication\ArticleResort','author_id','id');
    }

    //一对多关联取得我的自管理著作评审表
    public function getMyManageArticleReviews(){
        return $this->hasMany('App\Home\Publication\ArticleReview','initiate_id','id');
    }

    //一对多关联取得我的自管理著作攻辩表A
    public function getMyAArticleDebates(){
        return $this->hasMany('App\Home\Publication\ArticleDebate','Aauthor_id','id');
    }

    //一对多关联取得我的自管理著作攻辩表B
    public function getMyBArticleDebates(){
        return $this->hasMany('App\Home\Publication\ArticleDebate','Bauthor_id','id');
    }

    //一对多关联取得我的自管理著作攻辩表R
    public function getMyRArticleDebates(){
        return $this->hasMany('App\Home\Publication\ArticleDebate','referee_id','id');
    }

    //一对多关联取得我的著作反对表（要筛选pid=0的情况）
    public function getMyArticleOpponents(){
        return $this->hasMany('App\Home\Publication\ArticleDiscussion\ArticleOpponent','author_id','id');
    }

    //一对多关联取得我的著作建议表（要筛选pid=0的情况）
    public function getMyArticleAdvises(){
        return $this->hasMany('App\Home\Publication\ArticleDiscussion\ArticleAdvise','author_id','id');
    }

    //一对多关联取得我的自管理著作表
    public function getMyManageExams(){
        return $this->hasMany('App\Home\Examination\Exam','manage_id','id');
    }

    //一对多关联取得我的自管理著作协作计划表
    public function getMyManageExamCooperations(){
        return $this->hasMany('App\Home\Examination\ExamCooperation','manage_id','id');
    }

    //一对多关联取得我的自管理著作求助表（要筛选pid=0的情况）
    public function getMyManageExamResorts(){
        return $this->hasMany('App\Home\Examination\ExamResort','author_id','id');
    }

    //一对多关联取得我的自管理著作评审表
    public function getMyManageExamReviews(){
        return $this->hasMany('App\Home\Examination\ExamReview','initiate_id','id');
    }

    //一对多关联取得我的自管理著作攻辩表A
    public function getMyAExamDebates(){
        return $this->hasMany('App\Home\Examination\ExamDebate','Aauthor_id','id');
    }

    //一对多关联取得我的自管理著作攻辩表B
    public function getMyBExamDebates(){
        return $this->hasMany('App\Home\Examination\ExamDebate','Bauthor_id','id');
    }

    //一对多关联取得我的自管理著作攻辩表R
    public function getMyExamDebates(){
        return $this->hasMany('App\Home\Examination\ExamDebate','referee_id','id');
    }

    //一对多关联取得我的著作反对表（要筛选pid=0的情况）
    public function getMyExamOpponents(){
        return $this->hasMany('App\Home\Examination\ExamDiscussion\ExamOpponent','author_id','id');
    }

    //一对多关联取得我的著作建议表（要筛选pid=0的情况）
    public function getMyExamAdvises(){
        return $this->hasMany('App\Home\Examination\ExamDiscussion\ExamAdvise','author_id','id');
    }

    //一对多关联取得我的组织
    public function getMyManageGroups(){
        return $this->hasMany('App\Home\Organization\Group','manage_id','id');
    }

    //多对多关联词条表，取得关注词条数据
    public function getFocusEntries(){
        return $this->belongsToMany('App\Home\Encyclopedia\Entry','entry_focus_users','user_id','eid');
    }
    //多对多关联用户表，取得收藏词条数据
    public function getCollectEntries(){
        return $this->belongsToMany('App\Home\Encyclopedia\Entry','entry_collect_users','user_id','eid');
    }

    //多对多关联著作表，取得关注著作数据
    public function getFocusArticles(){
        return $this->belongsToMany('App\Home\Publication\Article','article_focus_users','user_id','article_id');
    }

    //多对多关联著作表，取得收藏著作数据
    public function getCollectArticles(){
        return $this->belongsToMany('App\Home\Publication\Article','article_collect_users','user_id','article_id');
    }

    //多对多关联著作表，取得关注试卷数据
    public function getFocusExams(){
        return $this->belongsToMany('App\Home\Examination\Exam','exam_focus_users','user_id','exam_id');
    }

    //多对多关联著作表，取得收藏试卷数据
    public function getCollectExams(){
        return $this->belongsToMany('App\Home\Examination\Exam','exam_collect_users','user_id','exam_id');
    }

    //一对多关联词条内容表
    public function entryContent(){
        return $this->hasMany('App\Home\Encyclopedia\Entry\EntryContent','id','creator_id');
    }
    //一对一主专业
    public function getSpecialty(){
        return $this->hasOne('App\Home\Classification','id','specialty');
    }

    //一对一管理组
    public function getCommittee(){
        return $this->hasOne('App\Models\Committee\Committee','id','committee_id');
    }

    // 新注册用户,zhege函数暂时没有用了
    protected function newUser($username,$email,$role_id,$password){
        $result = User::create([
            'username'  => $username,
            'email'     => $email,
            'role_id'      => $role_id,
            'password'  => bcrypt($password),
        ]);
        event(new UserRegisterdEvent($result));
        return $result;
    }

    // 添加主专业
    protected function specialtyAdd($id,$spe){
        $result = User::where('id',$id)-> update([
            'specialty'  => $spe
        ]);
        return $result;
    }

    // 添加主组织
    protected function primaryGroup($id,$gid){
        $result = User::where('id',$id)-> update([
            'gid'  => $gid
        ]);
        return $result;
    }

    // 更新管理组
    protected function committeeUpdate($id,$role_id,$committee_id){
        $result = User::where('id',$id)-> update([
            'role_id'  => $role_id,
            'committee_id'  => $committee_id
        ]);
        return $result;
    }
    // 角色被清理
    protected function judgeRole($id,$role_id,$committee_id){
        $result = User::where('id',$id)->update([
            'role_id'  => $role_id,
            'committee_id'  => $committee_id
        ]);
        return $result;
    }

    //更新用户的经验值和成长值
    protected function expAndGrowValue($id,$expvalue,$growvalue){
        $user = User::find($id);
        $oldExpValue = $user->exp_value;
        $oldGrowValue = $user->grow_value;
        $newExpValue = $oldExpValue + $expvalue;
        $newGrowValue = $oldGrowValue + $growvalue;
        $result = User::where('id',$id)-> update([
            'exp_value' => $newExpValue,
            'grow_value' => $newGrowValue,
        ]);
        // 该user是未修改的user
        event(new UserCreditChangedEvent($user));
        return $result;
    }

    // 获取用户ip
    public static function getClientIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        if (getenv('HTTP_X_REAL_IP')) {
            $ip = getenv('HTTP_X_REAL_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
            $ips = explode(',', $ip);
            $ip = $ips[0];
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = '0.0.0.0';
        }
        return $ip;
    }
    
}
