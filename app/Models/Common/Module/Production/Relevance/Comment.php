<?php
namespace App\Models\Common\Module\Production\Relevance;

use App\Models\Base;
use App\Http\Constant\Status;
use App\Enum\Module\Production\CommentEnum;

/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2021-01-12
 *
 * 作品评论模型类
 */
class Comment extends Base
{
  // 表名
  protected $table = "module_production_comment";

  // 隐藏的属性
  protected $hidden = [
    'update_time'
  ];

  // 追加到模型数组表单的访问器
  protected $appends = [];

  // 批量添加
  protected $fillable = ['id'];


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-01-12
   * ------------------------------------------
   * 评论内容类型封装
   * ------------------------------------------
   *
   * 评论内容类型封装
   *
   * @param [type] $value [description]
   * @return [type]
   */
  public function getSuffixAttribute($value)
  {
    return CommentEnum::getSuffixStatus($value);
  }


  // 关联函数 ------------------------------------------------------

  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-01-11
   * ------------------------------------------
   * 作品评论与作品关联函数
   * ------------------------------------------
   *
   * 作品评论与作品关联函数
   *
   * @return [关联对象]
   */
  public function production()
  {
    return $this->belongsTo('App\Models\Common\Module\Production\Production', 'production_id', 'id');
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2021-01-11
   * ------------------------------------------
   * 作品评论与学员关联函数
   * ------------------------------------------
   *
   * 作品评论与学员关联函数
   *
   * @return [关联对象]
   */
  public function member()
  {
    return $this->belongsTo('App\Models\Common\Module\Member\Member', 'member_id', 'id');
  }
}
