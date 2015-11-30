var j = jQuery.noConflict();
j(document).ready(function() {
    j(document).on('mouseover', '.new_leave_status_wrapper div.approve', function() {
        j(this).children('img').attr('src', '/images/icons/leave/approved.png');
    });
    j(document).on('mouseout', '.new_leave_status_wrapper div.approve', function() {
        j(this).children('img').attr('src', '/images/icons/leave/approve.png');
    });
    j(document).on('mouseover', '.new_leave_status_wrapper div.decline', function() {
        j(this).children('img').attr('src', '/images/icons/leave/declined.png');
    });
    j(document).on('mouseout', '.new_leave_status_wrapper div.decline', function() {
        j(this).children('img').attr('src', '/images/icons/leave/decline.png');
    });
});
