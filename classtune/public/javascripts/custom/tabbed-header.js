/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * Author: N!K
 */

var jq = jQuery.noConflict();
jq(document).ready(function() {
    jq(".header-tabs li").click(function(){
        jq('#monthreport').html('');
        jq('.header-tabs li').removeClass('active');
        jq(this).addClass('active');
    });
    
    jq(".header-tabs li a").click(function(){
        jq('#monthreport').html('');
        jq('.header-tabs li').removeClass('active');
        jq(this).parent('li').addClass('active');
    });
    
    jq(".submenu li").click(function(){
        jq('#monthreport').html('');
        jq('.header-tabs li').removeClass('active');
        jq(this).parent('ul').parent('li').addClass('active');
    });
    
    jq(".submenu li a").click(function(){
        jq('#monthreport').html('');
        jq('.header-tabs li').removeClass('active');
        jq(this).parent('li').parent('ul').parent('li').addClass('active');
    });
    
});