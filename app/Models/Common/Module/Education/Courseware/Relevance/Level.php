<?php
namespace App\Models\Common\Module\Education\Courseware\Relevance;

use App\Models\Base;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-01-05
 *
 * 课件级别模型类
 */
class Level extends Base
{
  // 表名
  public $table = "module_courseware_level";

  // 隐藏的属性
  public $hidden = [
    'update_time'
  ];

  // 追加到模型数组表单的访问器
  public $appends = [
    'level'
  ];

  // 批量添加
  public $fillable = ['id'];


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-01-05
   * ------------------------------------------
   * 课程级别封装
   * ------------------------------------------
   *
   * 课程级别封装
   *
   * @param [type] $value [description]
   * @return [type]
   */
  public function getLevelAttribute($value)
  {
    return $this->minimum_age . '岁 - ' . $this->largest_age . '岁';
  }


  // 关联函数 ------------------------------------------------------

  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-01-06
   * ------------------------------------------
   * 课件级别与课件关联函数
   * ------------------------------------------
   *
   * 课件级别与课件关联函数
   *
   * @return [关联对象]
   */
  public function courseware()
  {
    return $this->belongsTo('App\Models\Common\Module\Education\Courseware\Courseware', 'courseware_id', 'id')
                ->where(['status'=>1]);
  }
}
