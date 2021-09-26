<div>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/wangeditor@latest/dist/wangEditor.min.js"></script>

    <div id="app" style="padding: 8px;" v-cloak>
        <div>
            <el-card>
                <h3>上传示例</h3>
                <div style="width:390px;" id="div1"></div>

                <el-button style="margin-top: 15px;" type="primary" @click="onSubmit">
                    提交
                </el-button>
            </el-card>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const E = window.wangEditor
            new Vue({
                el: '#app',
                data: {
                    editor : new E('#div1'),
                },
                mounted: function () {
                    window.addEventListener('ZTBCMS_UPLOAD_IMAGE', this.onUploadedImage.bind(this));
                    window.addEventListener('ZTBCMS_UPLOAD_VIDEO', this.onUploadedVideo.bind(this));
                    this.seteditor()
                },
                methods: {
                    seteditor : function () {
                        var that = this;
                        this.editor.config.height = 500;
                        this.editor.config.menus = [
                            'head',
                            'bold',
                            'fontSize',
                            'fontName',
                            'italic',
                            'underline',
                            'strikeThrough',
                            'indent',
                            'lineHeight',
                            'foreColor',
                            'backColor',
                            'link',
                            'list',
                            'todo',
                            'justify',
                            'quote',
                            'emoticon',
                            'table',
                            'code',
                            'splitLine',
                            'undo',
                            'redo',
                            'video',
                        ]

                        //CMS图片上传
                        class CmsImageMenu extends E.BtnMenu {
                            constructor(editor) {
                                const $elem = E.$(
                                    `<div class="w-e-menu" data-title="图片上传">
                    <i class="el-icon-upload2"  id="menu_y"></i>
                </div>`
                                )
                                super($elem, editor)
                            }
                            // 菜单点击事件
                            clickHandler() {
                                layer.open({
                                    type: 2,
                                    title: '',
                                    closeBtn: false,
                                    content: "{:api_url('common/upload.panel/imageUpload')}?is_private=0",
                                    area: ['720px', '550px'],
                                })
                            }
                            tryChangeActive() {}
                        }
                        this.editor.menus.extend('cmsImageMenuKey', CmsImageMenu);
                        this.editor.config.menus = this.editor.config.menus.concat('cmsImageMenuKey');

                        //CMS视频上传
                        class CmsVideoMenu extends E.BtnMenu {
                            constructor(editor) {
                                const $elem = E.$(
                                    `<div class="w-e-menu" data-title="视频上传">
                    <i class="el-icon-video-camera-solid"  id="menu_y"></i>
                </div>`
                                )
                                super($elem, editor)
                            }
                            // 菜单点击事件
                            clickHandler() {
                                layer.open({
                                    type: 2,
                                    title: '',
                                    closeBtn: false,
                                    content: "{:api_url('common/upload.panel/videoUpload')}",
                                    area: ['720px', '550px'],
                                })
                            }
                            tryChangeActive() {}
                        }
                        this.editor.menus.extend('cmsVideoMenuKey', CmsVideoMenu);
                        this.editor.config.menus = this.editor.config.menus.concat('cmsVideoMenuKey');

                        // 配置粘贴文本的内容处理
                        this.editor.config.pasteTextHandle = function (pasteStr) {
                            that.copyText(pasteStr);
                        }

                        this.editor.create();
                        this.getDetails();
                    },
                    onSubmit : function () {
                        var that = this;
                        var text =  that.editor.txt.html()
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
                                that.editor.txt.html(res.data.text)
                            }
                        });
                    },
                    //图片上传
                    onUploadedImage: function (event) {
                        var that = this;
                        var files = event.detail.files;
                        if (files) {
                            files.map(function (item) {
                                that.editor.cmd.do(
                                    "insertHTML",
                                    `<p data-we-empty-p=""><img src="` + item.fileurl + `" style="max-width:100%;" contenteditable="false"></p>`
                                );
                            })
                        }
                    },
                    //视频上传
                    onUploadedVideo : function (event) {
                        var that = this;
                        var files = event.detail.files;
                        if (files) {
                            files.map(function (item) {
                                that.editor.cmd.do(
                                    "insertHTML",
                                    `<p data-we-empty-p="">
                                        <video src="` + item.fileurl + `" controls="controls" contenteditable="false" style="max-width:100%;" ></p>`
                                );
                            })
                        }
                    },
                    //批量复制
                    copyText : function (text) {
                        var that = this;
                        var url = '{:api_url("/wangeditor/index/demo")}';
                        var data = {
                            text : text,
                            _action : 'copy_text'
                        };
                        that.httpPost(url, data, function (res) {
                            if (res.status) {
                                text = res.data.text;
                            }
                            that.editor.cmd.do("insertHTML", text);
                        });
                    }
                },
            })
        })
    </script>
</div>