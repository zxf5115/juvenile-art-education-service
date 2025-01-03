<?php
namespace App\Http\Controllers\Api\Module\Member\Relevance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Http\Constant\Code;
use App\Http\Constant\Parameter;
use App\Http\Controllers\Api\BaseController;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2020-12-24
 *
 * 会员红包控制器类
 */
class MoneyController extends BaseController
{
  protected $_model = 'App\Models\Api\Module\Member\Relevance\Money';

  protected $_where = [];

  protected $_params = [
    'type'
  ];

  protected $_order = [
    ['key' => 'create_time', 'value' => 'desc'],
  ];

  protected $_relevance = [];


  /**
   * @api {get} /api/member/money/list?page={page} 01. 会员红包列表(分页)
   * @apiDescription 获取当前会员的红包列表(分页)
   * @apiGroup 06. 会员红包模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmN"
   * }
   *
   * @apiParam {int} page 当前页数
   * @apiParam {int} type 红包类型 1:收入 2: 提现
   *
   * @apiSuccess (basic params) {Number} id 红包编号
   * @apiSuccess (basic params) {Number} member_id 会员编号
   * @apiSuccess (basic params) {Number} type 红包类型 1: 收入 2: 提现
   * @apiSuccess (basic params) {String} content 红包描述
   * @apiSuccess (basic params) {Number} money 红包金额
   * @apiSuccess (basic params) {Number} create_time 收入时间|提现时间
   *
   * @apiSampleRequest /api/member/money/list
   * @apiVersion 1.0.0
   */
  public function list(Request $request)
  {
    try
    {
      $condition = self::getCurrentWhereData();

      // 对用户请求进行过滤
      $filter = $this->filter($request->all());

      $condition = array_merge($condition, $this->_where, $filter);

      // 获取关联对象
      $relevance = self::getRelevanceData($this->_relevance, 'select');

      $response = $this->_model::getPaging($condition, $relevance, $this->_order);

      return self::success($response);
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      self::record($e);

      return self::error(Code::ERROR);
    }
  }


  /**
   * @api {get} /api/member/money/select 02. 会员红包列表(不分页)
   * @apiDescription 获取当前会员的红包列表(不分页)
   * @apiGroup 06. 会员红包模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmN"
   * }
   *
   * @apiParam {int} type 红包类型 1: 收入 2: 提现
   *
   * @apiSuccess (basic params) {Number} id 红包编号
   * @apiSuccess (basic params) {Number} member_id 会员编号
   * @apiSuccess (basic params) {Number} type 红包类型 1: 收入 2: 提现
   * @apiSuccess (basic params) {String} content 红包描述
   * @apiSuccess (basic params) {Number} money 红包金额
   * @apiSuccess (basic params) {Number} create_time 收入时间|提现时间
   *
   * @apiSampleRequest /api/member/money/select
   * @apiVersion 1.0.0
   */
  public function select(Request $request)
  {
    try
    {
      $condition = self::getCurrentWhereData();

      // 对用户请求进行过滤
      $filter = $this->filter($request->all());

      $condition = array_merge($condition, $this->_where, $filter);

      // 获取关联对象
      $relevance = self::getRelevanceData($this->_relevance, 'select');

      $response = $this->_model::getList($condition, $relevance, $this->_order);

      return self::success($response);
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      self::record($e);

      return self::error(Code::ERROR);
    }
  }


  /**
   * @api {post} /api/member/money/handle 03. 会员红包提现
   * @apiDescription 提现当前会员的红包金额
   * @apiGroup 06. 会员红包模块
   * @apiPermission jwt
   * @apiHeader {String} Authorization 身份令牌
   * @apiHeaderExample {json} Header-Example:
   * {
   *   "Authorization": "eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmN"
   * }
   *
   * @apiParam {double} money 提现金额
   * @apiParam {string} alipay_account 支付宝账户
   * @apiParam {string} alipay_name 支付宝姓名
   *
   * @apiSampleRequest /api/member/money/handle
   * @apiVersion 1.0.0
   */
  public function handle(Request $request)
  {
    $messages = [
      'money.required'          => '请您输入提现金额',
      'money.numeric'           => '提现金额不合法',
      'alipay_account.required' => '请您输入支付宝账户',
      'alipay_name.required'    => '请您输入支付宝姓名',
    ];

    $rule = [
      'money'          => 'required|numeric',
      'alipay_account' => 'required',
      'alipay_name'    => 'required',
    ];

    // 验证用户数据内容是否正确
    $validation = self::validation($request, $messages, $rule);

    if(!$validation['status'])
    {
      return $validation['message'];
    }
    else
    {
      $model = $this->_model::firstOrNew(['id' => $request->id]);

      DB::beginTransaction();

      try
      {
        $model->organization_id = self::getOrganizationId();
        $model->member_id       = self::getCurrentId();
        $model->type            = 2;
        $model->content         = Parameter::MONEY_CONTENT;
        $model->money           = $request->money;

        $response = $model->save();

        DB::commit();

        return self::success(Code::HANDLE_SUCCESS);
      }
      catch(\Exception $e)
      {
        DB::rollback();

        // 记录异常信息
        self::record($e);

        return self::error(Code::HANDLE_FAILURE);
      }
    }
  }
}
