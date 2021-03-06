<?php require ('function.php'); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="<?php echo _pub; ?>layui/css/layui.css"  media="all">
    </head>
    <body>    
        <form class="layui-form" style="margin-top: 20px;" id="from">
            <div class="layui-form-item">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block">
                    <div class="layui-form-mid layui-word-aux"><?php echo $data['phone'] ?></div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">修改密码</label>
                <div class="layui-input-block">
                    <input type="text" name="pwd" placeholder="如果不需要修改请留空.." class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">所属代理</label>
                <div class="layui-input-block">
                    <input type="text" name="agent" <?php if (empty($data['agentid'])) { ?>placeholder="请输入代理用户手机号码.."<?php } else { ?>value="<?php echo M::agent_phone($data['agentid']); ?>" <?php } ?> class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">支付宝费率</label>
                <div class="layui-input-block">
                    <input type="text" name="bank2alipay_withdraw" placeholder="请输入支付宝费率.." value="<?php echo $data['bank2alipay_withdraw']; ?>"  class="layui-input" style="width: 98%;">
                    <div class="layui-form-mid layui-word-aux">此处默认为百分比，如输入1即为1%，用户手续费=订单金额*手续费率</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <select name="status" style="width: 98%;">
                        <option value="0" <?php echo $data['status'] == '0' ? 'selected' : ''; ?>>未审核</option>
                        <option value="1" <?php echo $data['status'] == '1' ? 'selected' : ''; ?>>正常</option>
                        <option value="2" <?php echo $data['status'] == '2' ? 'selected' : ''; ?>>已冻结</option>
                    </select>
                </div>
            </div>



            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="button" lay-filter="add">确认修改</button>
                </div>
            </div>
        </form>

        <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
        <script src="<?php echo _pub; ?>js/jquery.min.js" charset="utf-8"></script>
        <script>
            layui.use(['form', 'layedit'], function () {
                var form = layui.form
                        , layer = layui.layer
                        , layedit = layui.layedit;
                //添加
                form.on('submit(add)', function () {
                    layer.load();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=user_edit&id=' . $data['id']; ?>",
                        data: $('#from').serialize(),
                        success: function (data) {
                            if (data.code == '200') {
                                layer.closeAll('loading');
                                layer.msg(data.msg, {icon: 1});
                                setTimeout(function () {
                                    location.href = '';
                                }, 2000);
                            } else {
                                layer.closeAll('loading');
                                layer.msg(data.msg, {icon: 2});
                            }
                        },
                        error: function (data) {
                            alert("error:" + data.responseText);
                        }
                    });

                });
            });
        </script>
    </body>
</html>