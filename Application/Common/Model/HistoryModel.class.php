<?php
namespace Common\Model;
use Common\Model\BaseModel;
class HistoryModel extends BaseModel{

    public function getHistoryNumber($cond=[])
    {
        $data = $this
            ->alias('h')
            ->join('__COMICS__ c ON c.id = h.comic_id')
            ->join('__READER__ r ON r.id = h.reader_id')
            ->where(array_filter($cond))
            ->count();
        return $data;
    }

    public function getHistoryData($cond=[])
    {
        $data = $this
            ->alias('h')
            ->join('__COMICS__ c ON c.id = h.comic_id')
            ->join('__READER__ r ON r.id = h.reader_id')
            ->field('h.*,c.title as comic_title,r.nickname')
            ->where(array_filter($cond))
            ->select();
        return $data;
    }

    /**
     * 获取指定读者历史记录
     * @param  int $readerId
     */
    public function getHistoryList($readerId)
    {
        $cond['h.reader_id'] = $readerId;
        $data = $this
            ->alias('h')
            ->join('__COMICS__ c ON c.id = h.comic_id')
            ->field('h.*,head,cover,title,brief,total_chapter')
            ->where($cond)
            ->select();

        $chapter = M('chapter');
        foreach ($data as $key => $value) {
            $cond_chapter = [
                'comic_id' => $value['comic_id'],
                'catalog'  => $value['chapter']
            ];
            $data[$key]['chapter_title'] = $chapter->where($cond_chapter)->getField('chapter_title'); // 章节标题
            $data[$key]['chapter_name'] = toChineseNumber($value['chapter']);
            $data[$key]['rate'] = intval($value['chapter'] / $value['total_chapter'] * 100); // 已读百分比
        }

        return $data;
    }


    /**
     * 更新阅读历史
     * @param  int $comicId 漫画ID
     * @param  int $readerId  读者ID
     * @param  int $chapter 阅读章节
     * @param  int $channel 途径
     */
    public function updateHistory($comicId, $chapter, $readerId, $channel){
        $data = [
            'comic_id'  => $comicId,
            'reader_id' => $readerId,
            'chapter'   => $chapter,
            'channel'   => $channel,
            'last_time' => date('Y-m-d H:i:s')
        ];

        $cond_comic['id'] = $comicId;
        $typeIds = M('comics')->where($cond_comic)->getField('type_ids');
        $data['type_ids'] = $typeIds;

        $cond = [
            'comic_id'  => $comicId,
            'reader_id' => $readerId,
        ];
        $historyInfo = $this->where($cond)->find();

        if ($historyInfo) {
            $this->where($cond)->save($data);
        } else {
            $this->add($data);
        }
    }
}