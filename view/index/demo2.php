<div>
    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <h3>手机</h3>
                <div style="margin-top: 15px;width: 390px;">
                <select-wangeditor
                      marking="mini_text"
                      :text="mini_text"
                      @change="wangeditorChange"
                >
                </select-wangeditor>
                </div>

                <h3>H5</h3>
                <div style="margin-top: 15px;">
                    <select-wangeditor
                            marking="h5_text"
                            :text="h5_text"
                            @change="wangeditorChange"
                    >
                    </select-wangeditor>
                </div>


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
                    mini_text : '',
                    h5_text : ''
                },
                mounted: function () {
                    this.getDetails();
                },
                methods: {
                    wangeditorChange : function (text,marking) {
                        if(marking === 'mini_text') {
                            this.mini_text = text;
                        }
                        if(marking === 'h5_text') {
                            this.h5_text = text;
                        }
                    },
                    onSubmit : function () {
                        var that = this;
                        var url = '{:api_url("/wangeditor/index/demo2")}';
                        var data = {
                            mini_text : that.mini_text,
                            h5_text : that.h5_text,
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
                        var url = '{:api_url("/wangeditor/index/demo2")}';
                        var data = {
                            _action : 'details'
                        };
                        that.httpPost(url, data, function (res) {
                            if (res.status) {
                                that.mini_text = res.data.mini_text;
                                that.h5_text = res.data.h5_text;
                            }
                        });
                    },
                },
            })
        })
    </script>
</div>