function loadNotificationCount()
{
    $.ajax({

        url: "/seller/notifications/unread-count",

        type: "GET",

        success: function(response)
        {
            $("#notificationCount").text(response.count);
        }

    });
}

function loadNotifications()
{
    $.ajax({

        url: "/seller/notifications/latest",

        type: "GET",

        success: function(response)
        {
            let html = '';

            if(response.notifications.length === 0)
            {
                html += `
                    <li class="text-center py-4">

                        <i class="feather icon-bell f-30 text-muted"></i>

                        <p class="mt-2 mb-0">

                            No Notifications

                        </p>

                    </li>
                `;
            }
            else
            {
                $.each(response.notifications,function(index,item){

                    html += `

                    <li>

                        <a
                            href="/seller/notifications/${item.id}/read"
                            class="text-decoration-none text-dark"
                        >

                            <div class="d-flex">

                                <div class="flex-shrink-0">

                                    <i class="feather icon-bell text-primary f-24"></i>

                                </div>

                                <div class="flex-grow-1 ms-3">

                                    <h6 class="mb-1">

                                        ${item.title}

                                    </h6>

                                    <p class="mb-1">

                                        ${item.message}

                                    </p>

                                    <small class="text-muted">

                                        ${item.created_at}

                                    </small>

                                </div>

                            </div>

                        </a>

                    </li>

                    `;
                });
            }

            $("#notificationList").html(html);

        }

    });
}

$(document).ready(function(){

    loadNotificationCount();

    loadNotifications();

    setInterval(function(){

        loadNotificationCount();

        loadNotifications();

    },2000);

});
