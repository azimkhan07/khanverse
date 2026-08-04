console.log("Notification");

$(function () {
    loadNotifications();
    setInterval(function () {
        loadNotifications();
    },3000);
});

function loadNotifications(){
    $.get(window.notificationUrl,function(res){
        $("#notificationCount").text(res.count);
        $("#notificationList").html(res.html);
    });
}
