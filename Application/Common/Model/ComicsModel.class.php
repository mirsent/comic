<?php
namespace Common\Model;
use Common\Model\BaseModel;
class ComicsModel extends BaseModel{

    protected $_auto=array(
        array('status','get_default_status',1,'callback'),
        array('created_at','get_datetime',1,'callback'),
        array('updated_at','get_datetime',3,'callback')
    );

    public function getComicNumber($cond){
        $data = $this
            ->alias('c')
            ->join('__RELEASE_TYPE__ rt ON rt.id = c.release_type_id')
            ->where(array_filter($cond))
            ->count();
        return $data;
    }

    public function getComicData($cond){
        $data = $this
            ->alias('c')
            ->join('__RELEASE_TYPE__ rt ON rt.id = c.release_type_id')
            ->field('c.*,release_type_name')
            ->where(array_filter($cond))
            ->select();
        $type = M('comic_type');
        foreach ($data as $key => $value) {
            $cond_type = [
                'status' => C('STATUS_Y'),
                'id'     => array('in', $value['type_ids'])
            ];
            $typeArr = $type->where($cond_type)->getField('comic_type_name', true);
            $data[$key]['type_names'] = implode('；', $typeArr);
        }
        return $data;
    }

    public function addComic($data){
        if (!$data = $this->create($data)) {
            return false;
        } else {
            $data['type_ids'] = implode(',', $data['type_ids']);
            $res = $this
                ->add($data);
            return $res;
        }
    }

    public function editComic($cond, $data){
        if (!$data = $this->create($data)) {
            return false;
        } else {
            $data['type_ids'] = implode(',', $data['type_ids']);
            $res = $this
                ->where($cond)
                ->save($data);
            return $res;
        }
    }

    /**
     * 获取漫画信息
     * @param int $comicId 漫画ID
     */
    public function getComicInfo($comicId){
        $cond['id'] = $comicId;
        $data = $this
            ->where($cond)
            ->field('id as comic_id,total_chapter,pre_chapter_pay,pre_chapter_share,max_share_chapter')
            ->find();

        return $data;
    }

    /**
     * 获取漫画图片详情
     * @param  int $comicId 漫画ID
     * @param  int $chapter 章节
     * @return arr          漫画图片数组
     */
    public function getComicDetail($comicId, $chapter){
        $path = "Uploads/comic/".$comicId."/".$chapter."/*";
        $folder = glob($path);
        asort($folder,SORT_NATURAL);
        return array_values($folder);
    }

    /**
     * 验证费用
     * @param  int $comicId 漫画ID
     * @param  int $chapter 章节
     * @param  string $openid  字符串
     * @return 1：免费 2：已购买 -1：未购买
     */
    public function checkCost($comicId, $chapter, $openid){
        $comicInfo = $this->find($comicId);
        $freeChapter = $comicInfo['free_chapter'];

        if ($chapter <= $freeChapter) {
            // 免费章节
            return 1;
        } else {
            // 付费章节
            $cond_consume = [
                'comic_id' => $comicId,
                'openid'   => $openid,
                'chapter'  => $chapter,
                'status'   => C('STATUS_Y')
            ];
            $consumeInfo = M('consume_order')->where($cond_consume)->find();
            if ($historyInfo) {
                // 已购买
                return 2;
            } else {
                // 未购买
                return '-1';
            }
        }
    }

}