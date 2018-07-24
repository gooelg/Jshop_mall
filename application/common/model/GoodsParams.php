<?php
namespace app\common\model;
use app\common\model\GoodsTypeParams;
/**
 * 商品参数
 * Class GoodsParams
 * @package app\common\model
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:10
 */
class GoodsParams extends Common
{


    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    /**
     * 默认排序
     * @param $post
     * @return mixed
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-11 16:32
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['seller_id'])&&$post['seller_id']){
            $where[] = ['seller_id','eq',$post['seller_id']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id'=>'desc'];
        return $result;
    }


    /**
     * 数据转换
     * User:wjima
     * Email:1457529125@qq.com
     * @param $list
     * @return array
     */
    protected function tableFormat($list)
    {
        if($list)
        {
            foreach((array)$list->toArray() as $key=>$val)
            {
                $list[$key]['seller_name'] = getSellerInfoById($val['seller_id'],'seller_name');
            }
        }
        return $list;
    }

    /**
     * 添加商品参数
     * @param $data
     * @return int|string
     */
    public function doAdd($data = [],$id=0)
    {
        if($id>0){
            $result = $this->save($data,['id'=>$id]);
        }else{
            $result = $this->save($data);

        }
        if ($result) {
            return $this->getLastInsID();
        }
        return $result;
    }

    /**
     * 删除商品参数
     * @param array $filter
     * @return bool|int
     */
    public function doDel($filter = [])
    {
        if (!$filter['id']) {
            return false;
        }
        //删除关联表
        $typeParams      = new GoodsTypeParams();
        $typeParamsModel = $typeParams->where(['params_id' => $filter['id']]);
        if ($typeParamsModel->find()) {
            $res = $typeParamsModel->delete();
            if ($res) {
                return $this->where($filter)->delete();
            } else {
                return false;
            }
        } else {
            return $this->where($filter)->delete();
        }
    }

    /**
     * 获取所有参数
     * @param int $seller_id
     * @return array
     */
    public function getAllParams($seller_id = 0)
    {
        $list = $this->where(['seller_id'=>$seller_id])->select();
        if(!$list->isEmpty()){
            return $list->toArray();
        }
        return [];
    }

    /**
     * 获取参数信息
     * @param int $params_id
     * @param int $seller_id
     * @return array|bool
     */
    public function getParamsInfo($params_id=0,$seller_id=0)
    {
        if(!$params_id&&!$seller_id){
            return false;
        }
        $filter = [];
        if($params_id){
            $filter['id'] = $params_id;
        }
        if($seller_id){
            $filter['seller_id'] = $seller_id;
        }

        $info = $this->where($filter)->field('id,name,value,type')->find();
        if($info){
            if($info['value']){
                $info['value'] = explode(' ',$info['value']);
            }
            return $info->toArray();
        }else{
            return false;
        }
    }
}