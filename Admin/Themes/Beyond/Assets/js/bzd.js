/**
 * 通用JS
 * buzhidao
 * 2015-07-12
 */

/**
 * 显示/隐藏alert-panel
 * @param  string status alert元素状态 success:成功 error:错误
 * @param  string html   提示信息或者要显示的html内容
 */
var alertPanelShow = function(status, html) {
    var alertPanelObj = $("#alert-panel");
    var alertElementObj = $("#alert-element");

    var alerthtml = alertElementObj.find("div#alert-"+status).html();
    alerthtml = alerthtml.replace('{html}', html);
    alertPanelObj.html(alerthtml+alertPanelObj.html());

    //5秒后淡出
    setTimeout(function() {
        alertPanelObj.find(".alert:last").remove();
    }, 5000);
};

//AJAX回调函数
var ajaxCallback = function(data) {
    var status = data.error ? 'error' : 'success';
    alertPanelShow(status, data.msg);
    if (!data.error) {
        $("button.submit").addClass('disabled');
        setTimeout(function (){
            if ("location" in data.data) {
                location.href = data.data.location;
            }
            location.reload();
        }, 3000);
    }
};

//AJAX-form提交
$("form[name=ajax-form]").submit(function() {
    var url = $(this).attr("action");
    var method = $(this).attr("method");
    var d = $(this).serialize();

    if (method != "post") {
        $.post(url, d, ajaxCallback, 'json');
    } else {
        $.get(url, d, ajaxCallback, 'json');
    }

    return false;
});

//AJAX请求 启用/禁用、显示/隐藏、删除
$("a.btn-ajax, a.btn-ajax-enable, a.btn-ajax-delete").on('click', function (){
    $.post($(this).attr('href'), {}, function (data){
        ajaxCallback(data);
    }, 'json');

    return false;
});

//初始化bootbox
function bootboxInit() {
    var modalhtml = $("#modalhtml").html();
    var title = $("#modalhtml").attr("title");
    var className = $("#modalhtml").attr("class");

    $("#modalhtml").html(null);
    //初始化bootbox
    bootbox.dialog({
        title: title,
        message: modalhtml,
        className: className,
        buttons: {
            success: {
                label: "确定",
                className: "btn-sky",
                callback: function (event){
                    //绑定Form表单submit事件
                    $("form[name=modalform]").submit(function (event){
                        event.preventDefault();

                        var url = $(this).attr('doaction');
                        var postdata = $(this).serialize();
                        $.post(url, postdata, function (data){
                            var status = data.error ? 'error' : 'success';
                            window.frames["main"].alertPanelShow(status, data.msg);
                            if (!data.error) {
                                setTimeout(function (){
                                    if ("location" in data.data) {
                                        window.frames["main"].location.href = data.data.location;
                                    }
                                    window.frames["main"].location.reload();
                                }, 3000);
                            }
                        }, 'json');
                        return false;
                    });
                    //提交FORM 保存试题信息
                    $("form[name=modalform]").submit();
                }
            },
            danger: {
                label: "取消",
                className: "btn-danger",
                callback: function (){}
            }
        }
    });
}

$(function (){
    //modal-edit
    $("a.btn-edit-modal").on('click', function (){
        $.get($(this).attr("href"), {}, function (data){
            if (!data.error) {
                $(window.parent.document).find("#modalpanel").html(data.data.html).show();
                window.parent.bootboxInit();
            } else {
                alertPanelShow('error', data.msg);
            }
        });
        return false;
    });
});