<script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/wangeditor@latest/dist/wangEditor.min.js">
</script>

<script type="text/x-template" id="select-wangeditor">
    <div class="select-wangeditor">
        <div style="width:100%;" :id="marking"></div>
    </div>
</script>

<script>
    $(function () {
        const E = window.wangEditor
        Vue.component('select-wangeditor', {
            template: '#select-wangeditor',
            props: {
                marking : {
                    type: [String],
                    default: function () {
                        return 'div1'
                    }
                },
                text : {
                    type: [String],
                    default: function () {
                        return ''
                    }
                },
            },
            watch: {
                text: function (text) {
                    this.text = text
                    this.__initialization(text)
                },
            },
            data: function () {
                return {
                    is_initialization : false
                }
            },
            mounted: function () {
                window.addEventListener('ZTBCMS_UPLOAD_IMAGE', this.onUploadedImage.bind(this));
                window.addEventListener('ZTBCMS_UPLOAD_VIDEO', this.onUploadedVideo.bind(this));
                this.seteditor()
            },
            beforeDestroy:function (){
                console.log('beforeDestroy',this.text);
                this.editor.txt.html(this.text)
            },
            methods: {
                //初始化项目
                __initialization  : function (text) {
                    if(!this.is_initialization) {
                        this.is_initialization = true;
                        this.editor.txt.html(text)
                    }
                },
                seteditor : function () {
                    var that = this;
                    that.editor = new E('#'+this.marking);

                    that.editor.config.height = 500;

                    //菜单显示
                    that.editor.config.menus = [
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

                    //粘贴文本的内容处理
                    this.editor.config.pasteTextHandle = function (pasteStr) {
                        that.copyText(pasteStr);
                    }

                    //编辑内容
                    this.editor.config.onchange = function (html) {
                        that.changeValue();
                    }
                    that.editor.create();
                },
                //同步去父组件
                changeValue: function () {
                    this.text = this.editor.txt.html();
                    this.$emit('change',this.text,this.marking)
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
                        that.changeValue();
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
                        that.changeValue();
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
                        that.changeValue();
                    });
                }
            }
        });
    })
</script>