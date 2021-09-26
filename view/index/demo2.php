<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <h3>上传示例</h3>
                <select-wangeditor
                      marking="div1"
                      :text="text"
                      @change="wangeditorChange"
                >
                </select-wangeditor>

                <el-button style="margin-top: 15px;" type="primary" @click="onSubmit">
                    提交
                </el-button>
            </el-card>
        </div>
    </div>

    {include file="components/select-wangeditor"}
    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    text : ''
                },
                mounted: function () {
                    this.getDetails();
                },
                methods: {
                    wangeditorChange : function (text) {
                        this.text = text;
                    },
                    onSubmit : function () {
                        var that = this;
                        var text =  that.text
                        var url = '{:api_url("/wangeditor/index/demo")}';
                        var data = {
                            text : text,
                            _action : 'submit'
                        };
                        that.httpPost(url, data, function (res) {
                            if (res.status) {
                                layer.msg('提交成功', {time: 1000}, function () {
                                    that.getDetails();
                                });
                            } else {
                                layer.msg(res.msg, {time: 1000});
                            }
                        });
                    },
                    getDetails : function () {
                        var that = this;
                        var url = '{:api_url("/wangeditor/index/demo")}';
                        var data = {
                            _action : 'details'
                        };
                        that.httpPost(url, data, function (res) {
                            if (res.status) {
                                that.text = res.data.text;
                            }
                        });
                    },
                },
            })
        })
    </script>
</div>