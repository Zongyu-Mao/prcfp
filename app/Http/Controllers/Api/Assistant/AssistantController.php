<?php

namespace App\Http\Controllers\Api\Assistant;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Models\Personnel\RoleRight\RoleRight;
use App\Home\Personnel\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Home\Classification;

class AssistantController extends Controller
{
    // 获取延伸阅读
    public function extend_reading(Request $request) {
    	$data = $request->data;
    	$id = $data['id'];
    	$scope = $data['scope'];
    	$es = '';
        $as = '';
 		$exs = '';
    	if($scope==1){
    		$b = Entry::find($id);
    	} else if($scope==2){
    		$b = Article::find($id);
    	}
    	$es = $b->extendedEntryReadings()->get();
        $as = $b->extendedArticleReadings()->get();
 		$exs = $b->extendedExamReadings()->get();
 		return [
 			'es'	=>	$es,
 			'as'	=>	$as,
 			'exs'	=>	$exs,
 		];
    }

    // 获取权限
    public function checkRoleRight(Request $request) {
        $data = $request->data;
        $right_name = $data['right_name'];
        $scope = $data['scope'];
        
        $right = RoleRight::where('right',$right_name)->first();
        $role = $right->role;
        $committee = Auth::user()->getCommittee;
        // 有无权限直接后台check了，不转到前端了
        $msg = '';
        $hasRight = 0;
        $pl = $role->power_level;
        $path = '';
        if(!$committee) {
            $msg = '你缺少了管理组权限!';
        } else {
            // 这里要转化一下组织等级，因为组织等级和power_level量级是反的
            // 如果hierarchy是0，则直接通过了
            $hierarchy = 5-$committee->hierarchy;
            if($pl > $hierarchy) {
                $msg = '你的角色等级不符合权限要求!';
            } else {
                if($hierarchy == 0) {
                    $hasRight = 1;
                } else {
                    if($data['needPath']) {
                        $cid = $data['cid'];
                        $path = Classification::getClassPath($cid);
                        if(($hierarchy==1 && $committee->tcid==$path['class1_id'])
                            ||($hierarchy==2 && $committee->scid==$path['class2_id'])
                            ||($hierarchy==3 && $committee->thcid==$path['class3_id'])
                            ||($hierarchy==4 && $committee->cid==$cid)) {
                            $hasRight = 1;
                        } else {
                            $msg = '你的主专业不对应对象内容分类!';
                        }
                    } else {
                        // 不需要分类要求的，只需要满足power_level就可以了，其他特殊情况再说吧
                        $hasRight = 1;
                    }
                }
                
            }
        }
        

        return [
            'data'  =>  $data,
            'right'  =>  $right,
            'committee'  =>  $committee,
            'hasRight'  =>  $hasRight,
            'msg'       =>  $msg,
            'path'      =>  $path,
        ];
    }
}
