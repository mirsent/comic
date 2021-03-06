<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
class GatherController extends AdminBaseController {

    /******************************************* 画册 *******************************************/

    /**
     * 获取画册列表信息
     */
    public function get_gather_info()
    {
        $ms = D('Gather');

        $cond['g.status'] = C('APPLY_P');

        $recordsTotal = $ms->alias('g')->where($cond)->count();

        // 搜索
        $search = I('search');
        if (strlen($search)>0) {
            $cond['gather_title|nickname'] = array('like', '%'.$search.'%');
        }
        $cond['gather_title'] = I('gather_title');
        $cond['nickname'] = I('nickname');
        $searchDate = I('search_date');
        if ($searchDate) {
            $cond['publish_time'] = array('BETWEEN', [$searchDate.' 00:00:00', $searchDate.' 23:59:59']);
        }

        $recordsFiltered = $ms->getGatherNumber($cond);

        // 排序
        $orderObj = I('order')[0];
        $orderColumn = $orderObj['column']; // 排序列，从0开始
        $orderDir = $orderObj['dir'];       // ase desc
        if(isset(I('order')[0])){
            $i = intval($orderColumn);
            switch($i){
                case 1: $ms->order('gather_title '.$orderDir); break;
                case 2: $ms->order('likes '.$orderDir); break;
                case 3: $ms->order('nickname '.$orderDir); break;
                case 4: $ms->order('publish_time '.$orderDir); break;
                default: break;
            }
        } else {
            $ms->order('publish_time desc');
        }

        // 分页
        $start = I('start');  // 开始的记录序号
        $limit = I('limit');  // 每页显示条数
        $page = I('page');    // 第几页

        $infos = $ms->page($page, $limit)->getGatherData($cond);

        echo json_encode(array(
            "draw" => intval(I('draw')),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 新增 / 编辑画册
     */
    public function input_gather()
    {
        $rules = array (
            array('status','2'),
            array('publisher_id','-1'),
            array('like','0'),
            array('publish_time','get_datetime',1,'callback')
        );
        $gather = D('Gather');
        $gather->auto($rules)->create();

        $id = I('id');
        if ($id) {
            $cond['id'] = $id;
            $res = $gather->where($cond)->save();
        } else {
            $res = $gather->add();
        }

        if ($res === false) {
            ajax_return(0, '编辑画册失败');
        }
        ajax_return(1);
    }

    /**
     * 删除画册
     */
    public function delete_gather()
    {
        $cond['id'] = I('id');
        $data['status'] = C('STATUS_N');
        $res = M('gather')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0, '删除画册失败');
        }
        ajax_return(1);
    }

    /**
     * 删除画册图片
     */
    public function delete_gather_img()
    {
        $index = I('key');
        $gatherId = I('gather_id');

        $urlArr = explode(',', I('url'));
        array_splice($urlArr, $index, 1);
        $url = implode(',', $urlArr);

        $cond['id'] = $gatherId;
        $data['url'] = $url;
        $res = M('gather')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0, '删除画册图片失败');
        }
        ajax_return(1);
    }




    /******************************************* 画册申请 *******************************************/

    public function get_gather_apply_info()
    {
        $ms = D('Gather');

        $cond['g.status'] = C('APPLY_I');

        $recordsTotal = $ms->alias('g')->where($cond)->count();

        // 搜索
        $search = I('search');
        if (strlen($search)>0) {
            $cond['gather_title|nickname'] = array('like', '%'.$search.'%');
        }
        $cond['gather_title'] = I('gather_title');
        $cond['nickname'] = I('nickname');
        $searchDate = I('search_date');
        if ($searchDate) {
            $cond['publish_time'] = array('BETWEEN', [$searchDate.' 00:00:00', $searchDate.' 23:59:59']);
        }

        $recordsFiltered = $ms->getGatherNumber($cond);

        // 排序
        $orderObj = I('order')[0];
        $orderColumn = $orderObj['column']; // 排序列，从0开始
        $orderDir = $orderObj['dir'];       // ase desc
        if(isset(I('order')[0])){
            $i = intval($orderColumn);
            switch($i){
                case 1: $ms->order('gather_title '.$orderDir); break;
                case 2: $ms->order('nickname '.$orderDir); break;
                case 3: $ms->order('publish_time '.$orderDir); break;
                default: break;
            }
        } else {
            $ms->order('publish_time desc');
        }

        // 分页
        $start = I('start');  // 开始的记录序号
        $limit = I('limit');  // 每页显示条数
        $page = I('page');    // 第几页

        $infos = $ms->page($page, $limit)->getGatherData($cond);

        echo json_encode(array(
            "draw" => intval(I('draw')),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ), JSON_UNESCAPED_UNICODE);
    }




    /******************************************* 画册驳回 *******************************************/

    public function get_gather_ban_info()
    {
        $ms = D('Gather');

        $cond['g.status'] = C('APPLY_B');

        $recordsTotal = $ms->alias('g')->where($cond)->count();

        // 搜索
        $search = I('search');
        if (strlen($search)>0) {
            $cond['gather_title|nickname'] = array('like', '%'.$search.'%');
        }
        $cond['gather_title'] = I('gather_title');
        $cond['nickname'] = I('nickname');
        $searchDate = I('search_date');
        if ($searchDate) {
            $cond['publish_time'] = array('BETWEEN', [$searchDate.' 00:00:00', $searchDate.' 23:59:59']);
        }

        $recordsFiltered = $ms->getGatherNumber($cond);

        // 排序
        $orderObj = I('order')[0];
        $orderColumn = $orderObj['column']; // 排序列，从0开始
        $orderDir = $orderObj['dir'];       // ase desc
        if(isset(I('order')[0])){
            $i = intval($orderColumn);
            switch($i){
                case 1: $ms->order('gather_title '.$orderDir); break;
                case 2: $ms->order('nickname '.$orderDir); break;
                case 3: $ms->order('publish_time '.$orderDir); break;
                default: break;
            }
        } else {
            $ms->order('publish_time desc');
        }

        // 分页
        $start = I('start');  // 开始的记录序号
        $limit = I('limit');  // 每页显示条数
        $page = I('page');    // 第几页

        $infos = $ms->page($page, $limit)->getGatherData($cond);

        echo json_encode(array(
            "draw" => intval(I('draw')),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 通过画册
     */
    public function pass_gather()
    {
        $gather = M('gather');

        $cond['id'] = I('id');
        $data['status'] = C('APPLY_P');
        $res = $gather->where($cond)->save($data);

        $gatherInfo = $gather->where($cond)->find();

        $data_notice = [
            'reader_id' => $gatherInfo['publisher_id'],
            'content' => '发布的画册 '.$gatherInfo['gather_title'].' 通过审核！',
            'notice_time' => date('Y-m-d H:i:s'),
            'status' => C('STATUS_Y')
        ];
        M('notice')->add($data_notice);

        if ($res === false) {
            ajax_return(0, '通过画册失败');
        }
        ajax_return(1);
    }

    /**
     * 驳回画册
     */
    public function ban_gather()
    {
        $gather = M('gather');

        $cond['id'] = I('id');
        $data['status'] = C('APPLY_B');
        $res = $gather->where($cond)->save($data);

        $gatherInfo = $gather->where($cond)->find();

        $data_notice = [
            'reader_id' => $gatherInfo['publisher_id'],
            'content' => '发布的画册 '.$gatherInfo['gather_title'].' 未通过审核...',
            'notice_time' => date('Y-m-d H:i:s'),
            'status' => C('STATUS_Y')
        ];
        M('notice')->add($data_notice);

        if ($res === false) {
            ajax_return(0, '驳回画册失败');
        }
        ajax_return(1);
    }




    /******************************************* 评论 *******************************************/

    public function get_comment_info(){
        $ms = D('GatherComment');
        $cond['gc.status'] = array('neq', C('STATUS_N'));

        $recordsTotal = $ms->alias('gc')->where($cond)->count();

        // 搜索
        $search = I('search');
        if (strlen($search)>0) {
            $cond['gather_title|nickname|comment_content'] = array('like', '%'.$search.'%');
        }
        $cond['gather_title'] = I('gather_title');
        $cond['nickname'] = I('nickname');
        $searchDate = I('search_date');
        if ($searchDate) {
            $cond['comment_time'] = array('BETWEEN', [$searchDate.' 00:00:00', $searchDate.' 23:59:59']);
        }

        $recordsFiltered = $ms->getCommentNumber($cond);

        // 排序
        $orderObj = I('order')[0];
        $orderColumn = $orderObj['column']; // 排序列，从0开始
        $orderDir = $orderObj['dir'];       // ase desc
        if(isset(I('order')[0])){
            $i = intval($orderColumn);
            switch($i){
                case 1: $ms->order('gather_title '.$orderDir); break;
                case 3: $ms->order('nickname '.$orderDir); break;
                case 4: $ms->order('comment_content '.$orderDir); break;
                case 5: $ms->order('comment_time '.$orderDir); break;
                case 6: $ms->order('s_show '.$orderDir); break;
                default: break;
            }
        } else {
            $ms->order('comment_time desc');
        }

        // 分页
        $start = I('start');  // 开始的记录序号
        $limit = I('limit');  // 每页显示条数
        $page = I('page');    // 第几页

        $infos = $ms->page($page, $limit)->getCommentData($cond);

        echo json_encode(array(
            "draw" => intval(I('draw')),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 设置评论显示状态
     */
    public function set_comment(){
        $cond['id'] = I('id');
        $data['status'] = I('status');
        $res = M('gather_comment')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0, '设置评论显示状态失败');
        }
        ajax_return(1);
    }




    /******************************************* 点赞 *******************************************/

    public function get_likes_info()
    {
        $ms = D('GatherLikes');

        $cond['l.status'] = C('STATUS_Y');

        $recordsTotal = $ms->alias('l')->where($cond)->count();

        // 搜索
        $search = I('search');
        if (strlen($search)>0) {
            $cond['gather_title|nickname'] = array('like', '%'.$search.'%');
        }
        $cond['gather_title'] = I('gather_title');
        $cond['nickname'] = I('nickname');
        $searchDate = I('search_date');
        if ($searchDate) {
            $cond['like_time'] = array('BETWEEN', [$searchDate.' 00:00:00', $searchDate.' 23:59:59']);
        }

        $recordsFiltered = $ms->getLikesNumber($cond);

        // 排序
        $orderObj = I('order')[0];
        $orderColumn = $orderObj['column']; // 排序列，从0开始
        $orderDir = $orderObj['dir'];       // ase desc
        if(isset(I('order')[0])){
            $i = intval($orderColumn);
            switch($i){
                case 0: $ms->order('gather_title '.$orderDir); break;
                case 1: $ms->order('nickname '.$orderDir); break;
                case 2: $ms->order('like_time '.$orderDir); break;
                default: break;
            }
        } else {
            $ms->order('like_time desc');
        }

        // 分页
        $start = I('start');  // 开始的记录序号
        $limit = I('limit');  // 每页显示条数
        $page = I('page');    // 第几页

        $infos = $ms->page($page, $limit)->getLikesData($cond);

        echo json_encode(array(
            "draw" => intval(I('draw')),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ), JSON_UNESCAPED_UNICODE);
    }
}