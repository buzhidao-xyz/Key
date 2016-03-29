/**
 * bootstrap fileinput上传插件 类
 * buzhidao
 * 2015-12-26
 */

//初始化BootstrapFileInputClass
var BootstrapFileInputClass = function () {
    var oFile = new Object();

    //上传回调缓存数据
    oFile.file = {
        filename: ''
    }

    //初始化fileinput控件（第一次初始化）
    oFile.Init = function(ctrlName, uploadUrl) {
        var ctrlObj = $('#' + ctrlName + 'file');

        if (uploadUrl) {
            showUpload = true;
        } else {
            showUpload = false;
        }

        //初始化上传控件的样式 单文件上传
        ctrlObj.fileinput({
            language: 'zh', //设置语言
            uploadUrl: uploadUrl, //上传的地址
            multiple: false,
            uploadAsync: true, //AJAX
            // allowedFileTypes: ["image", "video"],
            allowedFileExtensions: ['jpg', 'gif', 'png', 'pdf', 'doc', 'docx', 'zip', 'xls', 'xlsx'],//接收的文件后缀
            showCaption: true, //是否显示标题
            showPreview: false, //是否显示预览图
            showCancel: false, //是否显示取消按钮
            showRemove: false, //是否显示移除按钮
            showUpload: showUpload, //是否显示上传按钮
            browseClass: "btn btn-primary", //按钮样式   
            dropZoneEnabled: false,//是否显示拖拽区域
            //minImageWidth: 50, //图片的最小宽度
            //minImageHeight: 50,//图片的最小高度
            //maxImageWidth: 1000,//图片的最大宽度
            //maxImageHeight: 1000,//图片的最大高度
            maxFileSize: 20480,//单位为kb，如果为0表示不限制文件大小
            minFileCount: 0,
            maxFileCount: 1, //表示允许同时上传的最大文件个数
            enctype: 'multipart/form-data',
            validateInitialCount: true,
            overwriteInitial: false,
            previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
            msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
        });

        //上传成功回调
        ctrlObj.on("fileuploaded", function (event, data, previewId, index) {
            alertPanelShow('success', data.response.msg);
            //服务端返回file写入input
            $('input[name='+ctrlName+']').val(data.response.data.filepath+data.response.data.filename);
        });

        //上传失败回调
        ctrlObj.on("fileuploaderror", function (event, data, previewId, index) {
            alertPanelShow('error', data.response.msg);
        });
    }
    return oFile;
};