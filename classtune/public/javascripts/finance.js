String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

function reloadFeeSubmission(batch_id,date_id,student_id,submission_date)
{
    var year = submission_date.getFullYear();
    var month = submission_date.getMonth() + 1;
    var day = submission_date.getDate();
    if (month < 10) {
        month = "0" + month;
    }
    if (day < 10) {
        day = "0" + day;
    }
    var dt = year + '-' + month + '-' + day;
    new Ajax.Request('/finance/load_fees_submission_batch', 
          {
            asynchronous:true, 
            evalScripts:true, 
            parameters:'batch_id='+batch_id+'&date='+date_id+'&student='+student_id+'&submission_date='+dt+'&student_fees='+j("#is_student_fees").val()
          }
    )
}

function resetSN()
{
   var sn = 1;
   j('.sl_no').each(function(){
        j(this).html(sn);
        sn += 1;
   });
}

function isNumberKey(evt, obj, target) 
{
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if ( charCode == 13 )
    {
        if ( target == 'vat' )
        {
            j("#fee_" + target + "_chk").trigger('click');  
        }
        else if ( target == 'fine' )
        {
            j("#fee_" + target + "_chk").trigger('click');  
        }
        else if ( target == 'particular_extra' )
        {
            j(".chk_extra_particular").trigger('click');  
        }
        else if ( target == 'discount_extra' )
        {
            j(".chk_extra_discount").trigger('click');  
        }
        else if ( target == 'fine_discount' )
        {
            var obj_id = j(obj).attr('id');
            var id = obj_id.replace('fee_fine_discount_amount_','');
            j("#fee_fine_discount_chk_" + id).trigger('click');  
        }
        else
        {
          var obj_id = j(obj).attr('id');
          var id = obj_id.replace('fee_' + target + '_amount_','');

          if ( target == 'discount' )
          {
              if ( checkValidDiscount(id) )
              {
                  j("#fee_" + target + "_chk_" + id).trigger('click');  
              }
              else
              {
                  alert("Invalid Discount Amount");
              }
          }
          else
          {
              j("#fee_" + target + "_chk_" + id).trigger('click');  
          }
        }
        return false;
    }
    else
    {
      var value = obj.value;
      var dotcontains = value.indexOf(".") != -1;
      if (dotcontains)
          if (charCode == 46) return false;
      if (charCode == 46) return true;
      if (charCode == 37) return true;
      if (charCode == 39) return true;
      if (charCode > 31 && (charCode < 48 || charCode > 57))
          return false;
      return true;
    }
}

function checkKey(evt, obj, target) 
{
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if ( charCode == 27 )
    {
        if ( target == 'vat' )
        {
            var fee = parseFloat(j("#fee_amount_" + target + " span").html());
            j(obj).val(fee.toFixed(2));

            j("#fee_" + target + "_chk").trigger('click'); 
        }
        else if ( target == 'fine' )
        {
            var fee = parseFloat(j("#fee_amount_" + target + " span").html());
            j(obj).val(fee.toFixed(2));

            j("#fee_" + target + "_chk").trigger('click'); 
        }
        else if ( target == 'particular_extra' )
        {
            return true;
        }
        else if ( target == 'discount_extra' )
        {
            return true;
        }
        else
        {
          var obj_id = j(obj).attr('id');
          var id = obj_id.replace('fee_' + target + '_amount_','');

          var fee = parseFloat(j("#fee_amount_" + target + "_" + id + " span").html());
          j(obj).val(fee.toFixed(2));

          j("#fee_" + target + "_chk_" + id).trigger('click');  
        }
        return false;
    }
    else if ( charCode == 37 )
    {
        return true;
    }
    else if ( charCode == 39 )
    {
        return true;
    }
}

function calculateAmountToPay(fid) 
{
    var amount = 0;
    j(".fee_amount_particular").each(function(){
        if ( !j(this).hasClass('disabled') )
        {
            var id = this.id.replace('fee_amount_particular_','');
            var fee = parseFloat(j("#fee_particular_amount_" + id).val());
            amount += fee;
        }
    });
    j("#fee_total_amount").val(amount.toFixed(2));
    j("#fee_total_amount_label").html(amount.toFixed(2));

    if ( fid > 0 )
    {
      checkTotalDiscountValue(fid);
      calculateDiscount();
      calculateTotalFees();
    }
}

function calculateDiscount() 
{
    var amount = 0;
    j(".fee_amount_discount").each(function(){
        if ( !j(this).hasClass('disabled') )
        {
            var id = this.id.replace('fee_amount_discount_','');
            var fee = parseFloat(j("#fee_discount_amount_" + id).val());
            amount += fee;
        }
    });
    j("#discount_total_amount").val(amount.toFixed(2));
    j("#discount_total_amount_label").html(amount.toFixed(2));
    calculateTotalFees();
}

function calculateTotalFees() 
{
    var current_amount = j("#remaining_amount").val();
    var fee_amount = parseFloat(j("#fee_total_amount").val());
    var paid_amount = parseFloat(j("#payment_done_label").html());

    var discount_amount = 0;
    if ( j("#discount_total_amount").length > 0 )
    {
        discount_amount = parseFloat(j("#discount_total_amount").val());
    }

    var amount = fee_amount - discount_amount;

    if ( amount < 0 )
    {
        amount = 0;
    }

    if ( j("#fee_vat_amount").length > 0 )
    {
        var vat_amount = parseFloat(j("#fee_vat_amount").val());
        amount += vat_amount; 
    }

    /*if ( j("#fee_fine_amount").length > 0 )
    {
        var fine_amount = parseFloat(j("#fee_fine_amount").val());
        amount += fine_amount; 
    }*/

    var total_amount = parseFloat(amount + paid_amount);
    if (amount == 0)
    {
        j(".payment_details").parent('td').parent('tr').hide();
        j(".pay_fees_buttons").children('input').each(function(){
            j(this).hide();
        });
        j(".pay_fees_buttons").children('a').each(function(){
            j(this).hide();
        });
        j(".pay_fees_buttons").append("<span style='border: 1px solid #ccc; background: #F5F5F5; float: right; padding: 10px; border-radius: 6px;'>Amount to pay is Zero, Nothing to pay</span>");
        
        j("#total_payable_label").html(total_amount.toFixed(2));
        j("#total_amount").val(amount.toFixed(2));
        j("#total_amount_label").html(amount.toFixed(2));
        
        generateAmountToPay();
    }
    else
    {
        if ( current_amount == 0 && amount > 0)
        {
            j("#total_payable_label").parent('td').css('text-align', 'center')
            j("#total_payable_label").html('<i class="fa fa-spinner fa-spin"></i>');
            
            j("#payment_done_label").parent('td').css('text-align', 'center')
            j("#payment_done_label").html('<i class="fa fa-spinner fa-spin"></i>');
            
            j("#total_amount_label").parent('td').css('text-align', 'center')
            j("#total_amount_label").html('<i class="fa fa-spinner fa-spin"></i>');
            
            j("#remaining_amount_label").parent('td').css('text-align', 'center')
            j("#remaining_amount_label").html('<i class="fa fa-spinner fa-spin"></i>');
            
            var batch_id = j("#batch_id_particular").val();
            var date_id = j("#date_id_particular").val();
            var student_id = j("#student_id_particular").val();
            new Ajax.Request('/finance/load_fees_submission_batch', 
                    {
                      asynchronous:true, 
                      evalScripts:true, 
                      parameters:'batch_id='+batch_id+'&date='+date_id+'&student='+student_id+'&student_fees='+j("#is_student_fees").val()
                    }
            )
        }
        else
        {
            j("#total_payable_label").html(total_amount.toFixed(2));
            j("#total_amount").val(amount.toFixed(2));
            j("#total_amount_label").html(amount.toFixed(2));
            generateAmountToPay();
        }
    } 
}

function generateAmountToPay() 
{
    if ( j("#total_amount").length > 0 )
    {
      var fee_amount = parseFloat(j("#total_amount").val());
    }
    else
    {
      var fee_amount = parseFloat(j("#fee_total_amount").val());
    }

    var fine_amount = 0; 

    j(".fee_amount_fine").each(function(){
        if ( !j(this).hasClass('disabled') )
        {
            var fine = parseFloat(j("#fee_fine_amount").val());
            fine_amount += fine;
        }
    });

    var fine_discount = 0;
    j(".fee_fine_amount_discount").each(function(){
        if ( !j(this).hasClass('disabled') )
        {
            var id = this.id.replace('fee_fine_amount_discount_','');
            var fine = parseFloat(j("#fee_fine_discount_amount_" + id).val());
            fine_discount += fine;
        }
    });

    fine_amount = fine_amount - fine_discount;
    if ( fine_amount < 0 )
    {
        fine_amount = 0;
    }

    if ( j("#fine_amount_to_pay").length > 0 )
    {
      j("#fine_amount_to_pay").val(fine_amount.toFixed(2));
      j("#fine_amount_to_pay_label").html(fine_amount.toFixed(2));
    }

    var amount = fee_amount + fine_amount;
    if ( amount < 0 )
    {
        amount = 0;
    }

    j("#amount_to_pay").val(amount.toFixed(2));
    j("#amount_to_pay_label").html(amount.toFixed(2));

    j("#total_fees_payment_record").html(amount.toFixed(2));
    j("#tot_fee_amount").val(amount.toFixed(2));

    j("#remaining_amount").val(amount.toFixed(2));
    j("#remaining_amount_label").html(amount.toFixed(2));
}

function checkTotalDiscountValue(fid)
{
    var fee_particular_category_id = parseInt(j("#fee_category_" + fid).val());
    var feeTotalAmount = parseFloat(j("#fee_total_amount").val());
    j(".discount_category").each(function(){
        if ( this.value == 0 )
        {
            var discount_id = this.id.replace("discount_category_","");
            var discount_amount = parseFloat(j("#fee_discount_amount_" + discount_id).val());
            var remaining_amount = feeTotalAmount - discount_amount;
            if ( remaining_amount < 0 )
            {
              if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
              {
                  j("#fee_amount_discount_" + discount_id).addClass('disabled');
                  j("#fee_amount_discount_" + discount_id).css('border', 'none');
                  j("#fee_amount_discount_" + discount_id + " i").css('display', 'none');
                  j("#fee_amount_discount_" + discount_id).css('color', '#999');
                  j("#fee_amount_discount_" + discount_id + " span").css('color', '#999');
                  j("#fee_discount_" + discount_id).removeAttr('checked');
                  j("#fee_discount_fa_" + discount_id).removeClass("fa-check-square-o");
                  j("#fee_discount_fa_" + discount_id).addClass("fa-square-o");
              }
            }
        }
        else if ( this.value == fee_particular_category_id )
        {
            var discount_id = this.id.replace("discount_category_","");
            var discount_amount = parseFloat(j("#fee_discount_amount_" + discount_id).val());
            var fee_amount = parseFloat(j("#fee_particular_amount_" + fid).val());
            var remaining_amount = fee_amount - discount_amount;
            if ( remaining_amount < 0 )
            {
              if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
              {
                  j("#fee_amount_discount_" + discount_id).addClass('disabled');
                  j("#fee_amount_discount_" + discount_id).css('border', 'none');
                  j("#fee_amount_discount_" + discount_id + " i").css('display', 'none');
                  j("#fee_amount_discount_" + discount_id).css('color', '#999');
                  j("#fee_amount_discount_" + discount_id + " span").css('color', '#999');
                  j("#fee_discount_" + discount_id).removeAttr('checked');
                  j("#fee_discount_fa_" + discount_id).removeClass("fa-check-square-o");
                  j("#fee_discount_fa_" + discount_id).addClass("fa-square-o");
              }
            }
        }
    });
}

function disabled_discount_val(fee_particular_category_id, discount_ids)
{
    if (fee_particular_category_id != 0)
    {
        return ;
    }
    var a_discount_ids = discount_ids.split(",");
    j(".discount_category").each(function(){
        if ( this.value != fee_particular_category_id )
        {
            var discount_id = this.id.replace("discount_category_","");
            if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
            {
                j("#fee_amount_discount_" + discount_id).addClass('disabled');
                j("#fee_amount_discount_" + discount_id).css('border', 'none');
                j("#fee_amount_discount_" + discount_id + " i").css('display', 'none');
                j("#fee_amount_discount_" + discount_id).css('color', '#999');
                j("#fee_amount_discount_" + discount_id + " span").css('color', '#999');
                j("#fee_discount_" + discount_id).removeAttr('checked');
                j("#fee_discount_fa_" + discount_id).removeClass("fa-check-square-o");
                j("#fee_discount_fa_" + discount_id).addClass("fa-square-o");
                calculateAmountToPay(0);
                calculateDiscount();
            }
            for(var i=0; i<a_discount_ids.length; i++)
            {
                if ( discount_id == a_discount_ids[i] )
                {
                    if (j('.discount_tr').length < 2)
                    {
                        var html = '<tr id="discount_0" class="discount_tr" style="display: none;"><td class="col-1" style=" text-align: center;"><i class="fa fa-close" style="font-size: 16px; cursor: pointer; color: #990A10;" aria-hidden="true"></i>&nbsp;&nbsp;<span class="sl_no" style="color: #000;">0</span></td><td class="col-2" colspan="3"></td><td class="col-6" style="text-align: right; "><span style="color: #666; text-decoration: line-through;">0.00</span>&nbsp;&nbsp;&nbsp;</td></tr>'
                        j('.discount_tr').last().after(html);
                        j('.discount_tr').last().after(html);
                        j("#discount_" + discount_id).remove();
                        resetSN();
                    }
                    else
                    {
                      j("#discount_" + discount_id).remove();
                      resetSN();
                    }
                }
            }
        }
    });
}

function hide_discount_of_onetime(fee_particular_category_id, discount_ids)
{
    var a_discount_ids = discount_ids.split(",");
    j(".discount_category").each(function(){
        if ( parseInt(this.value) == parseInt(fee_particular_category_id) )
        {
            var particular_id = parseInt(this.value);
            var discount_id = this.id.replace("discount_category_","");
            var is_onetime = parseInt(j(this).data('is-onetime'));

            if ( is_onetime == 0 )
            {
              if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
              {
                  j("#fee_amount_discount_" + discount_id).addClass('disabled');
                  j("#fee_amount_discount_" + discount_id).css('border', 'none');
                  j("#fee_amount_discount_" + discount_id + " i").css('display', 'none');
                  j("#fee_amount_discount_" + discount_id).css('color', '#999');
                  j("#fee_amount_discount_" + discount_id + " span").css('color', '#999');
                  j("#fee_discount_" + discount_id).removeAttr('checked');
                  j("#fee_discount_fa_" + discount_id).removeClass("fa-check-square-o");
                  j("#fee_discount_fa_" + discount_id).addClass("fa-square-o");
                  calculateAmountToPay(0);
                  calculateDiscount();
              }
              j("#discount_" + discount_id).hide();
              for(var i=0; i<a_discount_ids.length; i++)
              {
                  if ( discount_id == a_discount_ids[i] )
                  {
                      if (j('.discount_tr').length < 2)
                      {
                          var html = '<tr id="discount_0" class="discount_tr" style="display: none;"><td class="col-1" style=" text-align: center;"><i class="fa fa-close" style="font-size: 16px; cursor: pointer; color: #990A10;" aria-hidden="true"></i>&nbsp;&nbsp;<span class="sl_no" style="color: #000;">0</span></td><td class="col-2" colspan="3"></td><td class="col-6" style="text-align: right; "><span style="color: #666; text-decoration: line-through;">0.00</span>&nbsp;&nbsp;&nbsp;</td></tr>'
                          j('.discount_tr').last().after(html);
                          j('.discount_tr').last().after(html);
                          j("#discount_" + discount_id).remove();
                          resetSN();
                      }
                      else
                      {
                        j("#discount_" + discount_id).remove();
                        resetSN();
                      }
                  }
              }
            }
        }
        else
        {
            var particular_id = parseInt(this.value);
            if ( particular_id == 0 )
            {
                var discount_id = this.id.replace("discount_category_","");
                var is_onetime = parseInt(j(this).data('is-onetime'));

                if ( is_onetime == 0 )
                {
                  if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
                  {
                      j("#fee_amount_discount_" + discount_id).addClass('disabled');
                      j("#fee_amount_discount_" + discount_id).css('border', 'none');
                      j("#fee_amount_discount_" + discount_id + " i").css('display', 'none');
                      j("#fee_amount_discount_" + discount_id).css('color', '#999');
                      j("#fee_amount_discount_" + discount_id + " span").css('color', '#999');
                      j("#fee_discount_" + discount_id).removeAttr('checked');
                      j("#fee_discount_fa_" + discount_id).removeClass("fa-check-square-o");
                      j("#fee_discount_fa_" + discount_id).addClass("fa-square-o");
                      calculateAmountToPay(0);
                      calculateDiscount();
                  }
                  j("#discount_" + discount_id).hide();
                  for(var i=0; i<a_discount_ids.length; i++)
                  {
                      if ( discount_id == a_discount_ids[i] )
                      {
                          if (j('.discount_tr').length < 2)
                          {
                              var html = '<tr id="discount_0" class="discount_tr" style="display: none;"><td class="col-1" style=" text-align: center;"><i class="fa fa-close" style="font-size: 16px; cursor: pointer; color: #990A10;" aria-hidden="true"></i>&nbsp;&nbsp;<span class="sl_no" style="color: #000;">0</span></td><td class="col-2" colspan="3"></td><td class="col-6" style="text-align: right; "><span style="color: #666; text-decoration: line-through;">0.00</span>&nbsp;&nbsp;&nbsp;</td></tr>'
                              j('.discount_tr').last().after(html);
                              j('.discount_tr').last().after(html);
                              j("#discount_" + discount_id).remove();
                              resetSN();
                          }
                          else
                          {
                            j("#discount_" + discount_id).remove();
                            resetSN();
                          }
                      }
                  }
                }
            }
        }
    });
}

function reload_discount(discount_id_to_disabled)
{
    j(".discount_category").each(function(){
          var discount_id = parseInt(this.id.replace("discount_category_",""));

          if ( parseInt(discount_id_to_disabled) == discount_id )
          {
              if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
              {
                  j("#fee_amount_discount_" + discount_id).addClass('disabled');
                  j("#fee_amount_discount_" + discount_id).css('border', 'none');
                  j("#fee_amount_discount_" + discount_id + " i").css('display', 'none');
                  j("#fee_amount_discount_" + discount_id).css('color', '#999');
                  j("#fee_amount_discount_" + discount_id + " span").css('color', '#999');
                  j("#fee_discount_" + discount_id).removeAttr('checked');
                  j("#fee_discount_fa_" + discount_id).removeClass("fa-check-square-o");
                  j("#fee_discount_fa_" + discount_id).addClass("fa-square-o");
                  calculateAmountToPay(0);
                  calculateDiscount();
              }
              if (j('.discount_tr').length < 2)
              {
                  var html = '<tr id="discount_0" class="discount_tr" style="display: none;"><td class="col-1" style=" text-align: center;"><i class="fa fa-close" style="font-size: 16px; cursor: pointer; color: #990A10;" aria-hidden="true"></i>&nbsp;&nbsp;<span class="sl_no" style="color: #000;">0</span></td><td class="col-2" colspan="3"></td><td class="col-6" style="text-align: right; "><span style="color: #666; text-decoration: line-through;">0.00</span>&nbsp;&nbsp;&nbsp;</td></tr>'
                  j('.discount_tr').last().after(html);
                  j('.discount_tr').last().after(html);
                  j("#discount_" + discount_id).remove();
                  resetSN();
              }
              else
              {
                j("#discount_" + discount_id).remove();
                resetSN();
              }
          }
    });
}

function checkValidDiscount(discount_id)
{
    var fee_particular_category_id = parseInt(j("#discount_category_" + discount_id).val());
    var discount_amount = parseFloat(j("#fee_discount_amount_" + discount_id).val());

    if ( fee_particular_category_id == 0 )
    {
        var feeTotalAmount = parseFloat(j("#fee_total_amount").val());
        var remaining_amount = feeTotalAmount - discount_amount;
        if ( remaining_amount < 0 )
        {
            return false;
        }
        return true;
    }
    else
    {
        var fnd = true;
        j(".fee_category").each(function(){
            if ( this.value == fee_particular_category_id )
            {
                var fee_id = this.id.replace("fee_category_","");
                var fee_amount = parseFloat(j("#fee_particular_amount_" + fee_id).val());
                var remaining_amount = fee_amount - discount_amount;
                if ( remaining_amount < 0 )
                {
                    fnd = false;
                    return ;
                }
            }
        });
        return fnd;
    }
}
    
function disableFineDiscountAlso()
{
    j(".fee_fine_amount_discount").addClass('disabled');
    j(".fee_fine_amount_discount").css('border', 'none');
    j(".fee_fine_amount_discount" + " i").css('display', 'none');
    j(".fee_fine_amount_discount").css('color', '#999');
    j(".fee_fine_amount_discount" + " span").css('color', '#999');
    j(".fee_fine_discount").removeAttr('checked');
    j(".fee_fine_discount_fa").removeClass("fa-check-square-o");
    j(".fee_fine_discount_fa").addClass("fa-square-o");
}    


function loadJS()
{    
    document.observe("dom:loaded", function() {
        j(document).on('keypress', '#fees_form', function(e){
            if (e.keyCode == 13) {
                return false;
            }
        });
        
        j('#hide2').hide();
        j('#active-batch-link').hide();
        
        j("#fees_submission_batch_id").select2();

        /* This code section can be found in _student_fees_submission.html.erb*/

        // j(document).on('click','#add_extra_particular',function(){
        //     if ( j("#particulars_tr_extra").length == 0 )
        //     {
        //       var html = '<tr id="particulars_tr_extra">';
        //       html += '<td class="col-1" style=" text-align: center;"><i id="remove-extra-particular" class="fa fa-minus-circle" style="font-size: 16px; color: #990A10; cursor: pointer;" aria-hidden="true"></i></td>';
        //       html += '<td class="col-2" style="font-size: 14px; font-weight: bold;">';
        //       html += 'Particular Name: &nbsp;&nbsp;&nbsp;<input type="text" name="extra_particular" id="extra_particular" value="" style="border-radius: 4px; border: 1px solid #999; padding: 5px; width: 60%;" />';
        //       html += '</td><td class="col-6" colspan="3" style="text-align: right;">';
        //       html += '<input type="text" name="extra_particular_amount" id="extra_particular_amount" value="" style="border-radius: 4px; border: 1px solid #999; padding: 4px; width: 40%;" onkeypress="return isNumberKey(event,this, \'particular_extra\')" onkeydown="checkKey(event,this, \'particular_extra\')" />&nbsp;&nbsp;<i class="fa fa-check-square chk_extra_particular" name="extra_particular_chk" id="extra_particular_chk" style="font-size: 16px; cursor: pointer; " aria-hidden="true"></i></td></tr>';
        //       j('.particulars_tr').last().after(html);
        //     }
        // });

        j(document).on('click','#add_extra_discount',function(){
            if ( j("#discount_tr_extra").length == 0 )
            {
              var select = j("#discount_on_tmp").html();
              var html = '<tr id="discount_tr_extra">';
              html += '<td class="col-1" style=" text-align: center;"><i id="remove-extra-discount" class="fa fa-minus-circle" style="font-size: 16px; color: #990A10; cursor: pointer;" aria-hidden="true"></i></td>';
              html += '<td class="col-2" style="font-size: 14px; font-weight: bold;">';
              html += '<div style="padding: 10px;"><label style="width: 30%; display: inline-block;">Discount On: </label>&nbsp;&nbsp;&nbsp;<select name="discount_on_tmp_create" id="discount_on_tmp_create" style="padding: 4px 12px; border-radius: 4px; border: 1px solid #ccc; height: 34px; width: 64%;">' + select + '</select></div>';
              html += '<div style="padding: 10px;"><label style="width: 30%; display: inline-block;">Discount Text: </label>&nbsp;&nbsp;&nbsp;<input type="text" name="extra_discount" id="extra_discount" value="" style="border-radius: 4px; border: 1px solid #ccc; height: 20px; padding: 5px 12px; width: 61%;" /></div>';
              html += '</td><td class="col-6" colspan="3" style="text-align: right;">';
              html += '<input type="text" name="extra_discount_amount" id="extra_discount_amount" value="" style="border-radius: 4px; border: 1px solid #999; padding: 4px; width: 40%;" onkeypress="return isNumberKey(event,this, \'discount_extra\')" onkeydown="checkKey(event,this, \'discount_extra\')" />&nbsp;&nbsp;<i class="fa fa-check-square chk_extra_discount" name="extra_discount_chk" id="extra_discount_chk" style="font-size: 16px; cursor: pointer; " aria-hidden="true"></i></td></tr>';
              j('.discount_tr').last().after(html);
              //j('#discount_on_tmp_create').focus();
            }
        });

        j(document).on('click','#remove-extra-discount',function(){
            j("#discount_tr_extra").remove();
        });

        j(document).on('click','#remove-extra-particular',function(){
            j("#particulars_tr_extra").remove();
        });

        j(document).on('change','#discount_on_tmp_create',function(){
            if ( j.trim(this.value) != "" )
            {
                var txt = j('#discount_on_tmp_create option:selected').text();
                j("#extra_discount").val(txt + " Discount (BDT)");
            }
            else
            {
                j("#extra_discount").val("");
            }
            //j("#particulars_tr_extra").remove();
        });
        
        j(document).off('click','.fees_particular_fa').on("click",".fees_particular_fa",function(){
            var id = this.id.replace('fee_particular_fa_','');
            if (j(this).hasClass("fa-check-square-o"))
            {
                var fee_particular_category_id = parseInt(j("#fee_category_" + id).val());

                j(".discount_category").each(function(){
                    if ( this.value == fee_particular_category_id )
                    {
                        var discount_id = this.id.replace("discount_category_","");
                        if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
                        {
                            j("#fee_amount_discount_" + discount_id).addClass('disabled');
                            j("#fee_amount_discount_" + discount_id).css('border', 'none');
                            j("#fee_amount_discount_" + discount_id + " i").css('display', 'none');
                            j("#fee_amount_discount_" + discount_id).css('color', '#999');
                            j("#fee_amount_discount_" + discount_id + " span").css('color', '#999');
                            j("#fee_discount_" + discount_id).removeAttr('checked');
                            j("#fee_discount_fa_" + discount_id).removeClass("fa-check-square-o");
                            j("#fee_discount_fa_" + discount_id).addClass("fa-square-o");
                        }
                    }
                });

                j("#fee_amount_particular_" + id).addClass('disabled');
                j("#fee_amount_particular_" + id).css('border', 'none');
                j("#fee_amount_particular_" + id + " i").css('display', 'none');
                j("#fee_amount_particular_" + id).css('color', '#999');
                j("#fee_amount_particular_" + id + " span").css('color', '#999');
                j("#fee_particular_" + id).removeAttr('checked');
                j(this).removeClass("fa-check-square-o");
                j(this).addClass("fa-square-o");

                calculateDiscount();
                calculateAmountToPay(id);
                var discount = Number(j("#discount_total_amount").val());
                var total_amount = Number(j("#fee_total_amount").val());
                if ( discount > total_amount )
                {
                    j(".discount_category").each(function(){
                        var discount_id = this.id.replace("discount_category_","");
                        if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
                        {
                            j("#fee_amount_discount_" + discount_id).addClass('disabled');
                            j("#fee_amount_discount_" + discount_id).css('border', 'none');
                            j("#fee_amount_discount_" + discount_id + " i").css('display', 'none');
                            j("#fee_amount_discount_" + discount_id).css('color', '#999');
                            j("#fee_amount_discount_" + discount_id + " span").css('color', '#999');
                            j("#fee_discount_" + discount_id).removeAttr('checked');
                            j("#fee_discount_fa_" + discount_id).removeClass("fa-check-square-o");
                            j("#fee_discount_fa_" + discount_id).addClass("fa-square-o");
                        }
                    });
                }
                calculateDiscount();
                calculateAmountToPay(id);
            }
            else
            {
                j("#fee_amount_particular_" + id).removeClass('disabled');
                j("#fee_amount_particular_" + id).css('border-bottom', '1px dashed #000');
                j("#fee_amount_particular_" + id + " i").css('display', 'inline-block');
                j("#fee_amount_particular_" + id).css('color', '#990A10');
                j("#fee_amount_particular_" + id + " span").css('color', '#990A10');
                j("#fee_particular_" + id).prop('checked', true);
                j(this).removeClass("fa-square-o");
                j(this).addClass("fa-check-square-o");
            }
            calculateAmountToPay(id);
        });

        j(document).on("click",".fee_fine_discount_fa",function(){
            var id = this.id.replace('fee_fine_discount_fa_','');
            if (j(this).hasClass("fa-check-square-o"))
            {
                j("#fee_fine_amount_discount_" + id).addClass('disabled');
                j("#fee_fine_amount_discount_" + id).css('border', 'none');
                j("#fee_fine_amount_discount_" + id + " i").css('display', 'none');
                j("#fee_fine_amount_discount_" + id).css('color', '#999');
                j("#fee_fine_amount_discount_" + id + " span").css('color', '#999');
                j("#fee_fine_discount_fa_" + id).removeAttr('checked');
                j(this).removeClass("fa-check-square-o");
                j(this).addClass("fa-square-o");
            }
            else
            {
                var fine_amount = 0; 

                j(".fee_amount_fine").each(function(){
                    if ( !j(this).hasClass('disabled') )
                    {
                        var fine = parseFloat(j("#fee_fine_amount").val());
                        fine_amount += fine;
                    }
                });

                var fine_discount = 0;
                j(".fee_fine_amount_discount").each(function(){
                    if ( !j(this).hasClass('disabled') )
                    {
                        var discount_id = this.id.replace('fee_fine_amount_discount_','');
                        var fine = parseFloat(j("#fee_fine_discount_amount_" + discount_id).val());
                        fine_discount += fine;
                    }
                    else
                    {
                        var discount_id = this.id.replace('fee_fine_amount_discount_','');
                        if ( discount_id == id )
                        {
                            var fine = parseFloat(j("#fee_fine_discount_amount_" + discount_id).val());
                            fine_discount += fine;
                        }
                    }
                });

                if ( fine_discount > fine_amount )
                {
                  alert("Sorry you can't able to Enable the fine Discount");
                }
                else
                {
                  j("#fee_fine_amount_discount_" + id).removeClass('disabled');
                  j("#fee_fine_amount_discount_" + id).css('border-bottom', '1px dashed #000');
                  j("#fee_fine_amount_discount_" + id + " i").css('display', 'inline-block');
                  j("#fee_fine_amount_discount_" + id).css('color', '#990A10');
                  j("#fee_fine_amount_discount_" + id + " span").css('color', '#990A10');
                  j("#fee_fine_discount_fa_" + id).prop('checked', true);
                  j(this).removeClass("fa-square-o");
                  j(this).addClass("fa-check-square-o");
                }
            }
            generateAmountToPay();
        });

        j(document).on("click",".fee_vat_fa",function(){
            if (j(this).hasClass("fa-check-square-o"))
            {
                j("#fee_amount_vat").addClass('disabled');
                j("#fee_amount_vat").css('border', 'none');
                j("#fee_amount_vat" + " i").css('display', 'none');
                j("#fee_amount_vat").css('color', '#999');
                j("#fee_amount_vat" + " span").css('color', '#999');
                j("#fee_vat").removeAttr('checked');
                j(this).removeClass("fa-check-square-o");
                j(this).addClass("fa-square-o");
            }
            else
            {
                if ( !j("#fee_amount_vat").hasClass("only_enable_vat") )
                {
                    j("#fee_amount_vat").removeClass('disabled');
                    j("#fee_amount_vat").css('border-bottom', '1px dashed #000');
                    j("#fee_amount_vat" + " i").css('display', 'inline-block');
                    j("#fee_amount_vat").css('color', '#990A10');
                    j("#fee_amount_vat" + " span").css('color', '#990A10');
                }
                else
                {
                    j("#fee_amount_vat").removeClass('disabled');
                    j("#fee_amount_vat").css('color', '#000');
                    j("#fee_amount_vat" + " span").css('color', '#000');
                }
                j("#fee_vat").prop('checked', true);
                j(this).removeClass("fa-square-o");
                j(this).addClass("fa-check-square-o");
            }
            calculateAmountToPay(id);
        });

        j(document).on("click",".fee_fine_fa",function(){
            if (j(this).hasClass("fa-check-square-o"))
            {
                j("#fee_amount_fine").addClass('disabled');
                j("#fee_amount_fine").css('border', 'none');
                j("#fee_amount_fine" + " i").css('display', 'none');
                j("#fee_amount_fine").css('color', '#999');
                j("#fee_amount_fine" + " span").css('color', '#999');
                j("#fee_fine").removeAttr('checked');
                disableFineDiscountAlso();

                j(this).removeClass("fa-check-square-o");
                j(this).addClass("fa-square-o");
            }
            else
            {
                j("#fee_amount_fine").removeClass('disabled');
                j("#fee_amount_fine").css('border-bottom', '1px dashed #000');
                j("#fee_amount_fine" + " i").css('display', 'inline-block');
                j("#fee_amount_fine").css('color', '#990A10');
                j("#fee_amount_fine" + " span").css('color', '#990A10');
                j("#fee_fine").prop('checked', true);
                j(this).removeClass("fa-square-o");
                j(this).addClass("fa-check-square-o");
            }
            generateAmountToPay();
        });

        j(document).on("click",".fees_discount_fa",function(){
            var id = this.id.replace('fee_discount_fa_','');
            if (j(this).hasClass("fa-check-square-o"))
            {
                j("#fee_amount_discount_" + id).addClass('disabled');
                j("#fee_amount_discount_" + id).css('border', 'none');
                j("#fee_amount_discount_" + id + " i").css('display', 'none');
                j("#fee_amount_discount_" + id).css('color', '#999');
                j("#fee_amount_discount_" + id + " span").css('color', '#999');
                j("#fee_discount_" + id).removeAttr('checked');
                j(this).removeClass("fa-check-square-o");
                j(this).addClass("fa-square-o");
            }
            else
            {
                var fee_particular_category_id = j("#discount_category_" + id).val();

                var discount_on_total_fees = 0;
                var discount_on_particular = 0;
                j(".discount_category").each(function(){
                   var discount_particular_category_id = this.value;
                   var discount_id = this.id.replace("discount_category_","");
                   if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
                   {
                       if ( discount_particular_category_id == 0 )
                       {
                           discount_on_total_fees++;
                       }
                       else if ( discount_particular_category_id > 0 )
                       {
                           discount_on_particular++;
                       }
                   }
                });

                if ( parseInt(fee_particular_category_id) == 0 )
                {
                      if ( discount_on_particular > 0 )
                      {
                        var confirm_user = confirm("If you want to enabled discount on total fees, your other discount on particulars will be disabled. do you wish to continue?");
                        if ( confirm_user == false )
                        {
                            return false;
                        }
                        disabled_discount_val(0);
                      }
                }
                else if ( parseInt(fee_particular_category_id) > 0 )
                {
                    if ( discount_on_total_fees > 0 )
                    {
                        alert("Please disabled discount on total fees first if you want to enabled this discount");
                        return false;
                    }
                }

                if ( fee_particular_category_id == 0 )
                {
                    var feeTotalAmount = parseFloat(j("#fee_total_amount").val());
                    var discountAmount = parseFloat(j("#fee_discount_amount_" + id).val()) + parseFloat(j("#discount_total_amount").val());
                    if ( discountAmount > feeTotalAmount )
                    {
                        return false;
                    }
                }
                else
                {
                  var fnd = false;
                  j(".fee_category").each(function(){
                      if ( this.value == fee_particular_category_id )
                      {
                          var fee_id = this.id.replace("fee_category_","");
                          if (j("#fee_amount_particular_" + fee_id).hasClass("disabled"))
                          {
                              fnd = true;
                          }
                          if ( ! fnd )
                          {
                            var fee_amount = parseFloat(j("#fee_particular_amount_" + fee_id).val());
                            var discountAmount = parseFloat(j("#fee_discount_amount_" + id).val());
                            if ( discountAmount > fee_amount )
                            {
                                fnd = true;
                            }
                          }
                      }
                  });
                  if ( fnd )
                  {
                      return false;
                  }
                }
                j("#fee_amount_discount_" + id).removeClass('disabled');
                j("#fee_amount_discount_" + id).css('border-bottom', '1px dashed #000');
                j("#fee_amount_discount_" + id + " i").css('display', 'inline-block');
                j("#fee_amount_discount_" + id).css('color', '#990A10');
                j("#fee_amount_discount_" + id + " span").css('color', '#990A10');
                j("#fee_discount_" + id).prop('checked', true);
                j(this).removeClass("fa-square-o");
                j(this).addClass("fa-check-square-o");
            }
            calculateDiscount();
            //calculateAmountToPay(id);
        });

        j(document).on("click",".fee_amount_particular",function(){
            if ( !j(this).hasClass('disabled') )
            {
                var id = this.id.replace('fee_amount_particular_','');
                j(this).hide();
                j("#fee_particular_amount_" + id).attr('type','text');
                j("#fee_particular_chk_" + id).show();
                j("#fee_particular_amount_" + id).focus();
                var data = j("#fee_particular_amount_" + id).val();
                j("#fee_particular_amount_" + id).val('').val(data);
            }
        });

        j(document).on("click",".fee_amount_discount",function(){
            if ( !j(this).hasClass('disabled') )
            {
                var id = this.id.replace('fee_amount_discount_','');
                j(this).hide();
                j("#fee_discount_amount_" + id).attr('type','text');
                j("#fee_discount_chk_" + id).show();
                j("#fee_discount_amount_" + id).focus();
                var data = j("#fee_discount_amount_" + id).val();
                j("#fee_discount_amount_" + id).val('').val(data);
            }
        });

        j(document).on("click",".fee_amount_vat",function(){
            if ( !j(this).hasClass('disabled') )
            {
                j(this).hide();
                j("#fee_vat_amount").attr('type','text');
                j("#fee_vat_chk").show();
                j(".assign_vat_to_user").show();
                j("#fee_vat_amount").focus();
                var data = j("#fee_vat_amount").val();
                j("#fee_vat_amount").val('').val(data);
            }
        });

        j(document).on("click",".fee_fine_amount_discount",function(){
            if ( !j(this).hasClass('disabled') )
            {
                var id = this.id.replace('fee_fine_amount_discount_','');
                j(this).hide();
                j("#fee_fine_discount_amount_" + id).attr('type','text');
                j("#fee_fine_discount_chk_" + id).show();
                j("#fee_fine_discount_amount_" + id).focus();
                var data = j("#fee_fine_discount_amount_" + id).val();
                j("#fee_fine_discount_amount_" + id).val('').val(data);
            }
        });

        j(document).on("click",".fee_amount_fine",function(){
            if ( !j(this).hasClass('disabled') )
            {
                j(this).hide();
                j("#fee_fine_amount").attr('type','text');
                j("#fee_fine_chk").show();
                j("#fee_fine_amount").focus();
                var data = j("#fee_fine_amount").val();
                j("#fee_fine_amount").val('').val(data);
            }
        });

        j(document).on("click",".chk_particular",function(){
              var id = this.id.replace('fee_particular_chk_','');
              j(this).hide();
              j("#fee_particular_amount_" + id).attr('type','hidden');
              j("#fee_amount_particular_" + id).show();
              j("#fee_amount_particular_" + id + " span").html(parseFloat(j("#fee_particular_amount_" + id).val()).toFixed(2));
              calculateAmountToPay(id);
        });

        j(document).on("click",".chk_vat",function(){
              j(this).hide();
              j("#fee_vat_amount").attr('type','hidden');
              j(".assign_vat_to_user").hide();
              j("#fee_amount_vat").show();
              j("#fee_amount_vat" + " span").html(parseFloat(j("#fee_vat_amount").val()).toFixed(2));
              calculateTotalFees(); 
        });

        j(document).on("click",".chk_fine",function(){
              j(this).hide();
              j("#fee_fine_amount").attr('type','hidden');
              j(".assign_fine_to_user").hide();
              j("#fee_amount_fine").show();
              j("#fee_amount_fine" + " span").html(parseFloat(j("#fee_fine_amount").val()).toFixed(2));
              generateAmountToPay(); 
        });

        j(document).on("click",".chk_fine_discount",function(){
              var id = this.id.replace('fee_fine_discount_chk_','');

              var fine_amount = 0; 

              j(".fee_amount_fine").each(function(){
                  if ( !j(this).hasClass('disabled') )
                  {
                      var fine = parseFloat(j("#fee_fine_amount").val());
                      fine_amount += fine;
                  }
              });

              var fine_discount = 0;
              j(".fee_fine_amount_discount").each(function(){
                  if ( !j(this).hasClass('disabled') )
                  {
                      var discount_id = this.id.replace('fee_fine_amount_discount_','');
                      var fine = parseFloat(j("#fee_fine_discount_amount_" + discount_id).val());
                      fine_discount += fine;
                  }
              });

              if ( fine_discount > fine_amount )
              {
                  alert("Total Discount on fine can't be greater than fine, Please correct your value and try again");
              }
              else
              {
                j(this).hide();
                j("#fee_fine_discount_amount_" + id).attr('type','hidden');
                j("#fee_fine_amount_discount_" + id).show();
                j("#fee_fine_amount_discount_" + id + " span").html(parseFloat(j("#fee_fine_discount_amount_" + id).val()).toFixed(2));
                generateAmountToPay(); 
              }
        });

        j(document).on("click",".remove_fine",function(){
              j("#fine_blank_space_1").remove();
              j("#fine_blank_space_2").remove();
              j("#fine_blank_space_3").remove();
              j("#fine_blank_space_4").remove();
              j("#extra_fine").remove();
              generateAmountToPay(); 
        });

        j(document).off('click','.chk_extra_particular').on("click",".chk_extra_particular",function(){
             j(this).hide();
             var particular_category_id = j.trim(j("#selectParticular").val());
             if ( particular_category_id == '' )
             {
                alert("Please Select a Particular Category");
                return ;
             }
             var particular_name = j.trim(j("#extra_particular_name").val());
             if ( particular_name == '' )
             {
                alert("Particular name can't be blank");
                return ;
             }
             particular_name = particular_name.replaceAll("&","--")
             var particular_amount = j.trim(j("#extra_particular_amount").val());
             if ( particular_amount == 0 )
             {
                alert("Particular amount Can't be Zero");
                return ;
             }else if (particular_amount == null){
                 alert("Particular amount Can't be Empty");
                 return ;
             }else {
                 particular_amount = parseFloat(particular_amount);
             }
             var html = '<tr id="particulars_tr_id"></tr>';
             j('.particulars_tr').last().after(html);

             j('#remove-extra-particular').removeClass("fa-minus-circle");
             j('#remove-extra-particular').addClass("fa-spinner");
             j('#remove-extra-particular').addClass("fa-spin");
             j('#remove-extra-particular').attr("id", "remove-extra-particular-spin");

             new Ajax.Request('/finance/create_fees_with_tmp_particular', 
                  {
                    asynchronous:true, 
                    evalScripts:true, 
                    parameters:'amount='+particular_amount+'&batch_id='+j("#batch_id_particular").val()+'&date='+j("#date_id_particular").val()+'&student='+j("#student_id_particular").val()+'&particular_category='+particular_category_id+'&particular='+particular_name+'&no_vat=1'+'&student_fees='+j("#is_student_fees").val()
                  }
             );
        });

        j(document).on("click",".chk_extra_discount",function(){
             var discount_name = j.trim(j("#extra_discount").val());
             if ( discount_name.length == 0 )
             {
                alert("Discount Text Can't be Empty");
                return ;
             }
             var discount_amount = parseFloat(j.trim(j("#extra_discount_amount").val()));
             if ( discount_amount == 0 )
             {
                alert("Discount amount Can't be Zero");
                return ;
             }

             var discount_on = j.trim(j("#discount_on_tmp_create").val());
             if ( discount_on == "" )
             {
                alert("Please select a particular, you want to give the discount");
                return ;
             }

             var discount_on_total_fees = 0;
             var discount_on_particular = 0;
             var tmp_discount_ids = "";
             j(".discount_category").each(function(){
                var discount_particular_category_id = this.value;
                var discount_id = this.id.replace("discount_category_","");
                //if (j("#fee_discount_fa_" + discount_id).hasClass("fa-check-square-o"))
                //{
                    if ( discount_particular_category_id == 0 )
                    {
                        discount_on_total_fees++;
                    }
                    else if ( discount_particular_category_id > 0 )
                    {
                        discount_on_particular++;
                        tmp_discount_ids += discount_id + ",";
                    }
                //}
             });

            if ( tmp_discount_ids.length > 0 )
            {
                tmp_discount_ids = tmp_discount_ids.substr(0, tmp_discount_ids.length - 1);
            }

             var forced_add_discount = true;
             var discount_ids = "";
             if ( parseInt(discount_on) == 0 )
             {

                  var has_repeat_discount = 0;
                  j(".discount_category").each(function(){
                      var discount_particular_category_id = this.value;

                      if ( parseInt(discount_particular_category_id) == 0 )
                      {
                          if ( parseInt(j(this).data('is-onetime')) == 0 )
                          {
                              var discount_id = this.id.replace("discount_category_","");
                              discount_ids = discount_id + ",";
                              has_repeat_discount++;
                          }
                      }
                      else
                      {
                          var discount_id = this.id.replace("discount_category_","");
                          discount_ids = discount_id + ",";
                      }
                  });

                  if ( discount_ids.length > 0 )
                  {
                      discount_ids = discount_ids.substr(0, discount_ids.length - 1);
                  }

                  if ( has_repeat_discount > 0 )
                  {
                      var forced_add_discount = confirm("Your previous discount on total fees will be overwrite by this discount, do you wish to continue?");
                  }
                  else
                  {
                      var feeTotalAmount = parseFloat(j("#fee_total_amount").val());
                      var discountAmount = discount_amount + parseFloat(j("#discount_total_amount").val());
                      if ( discountAmount > feeTotalAmount )
                      {
                          alert("Discount Amount can't be greater than fee amount");
                          return ;
                      }
                      else
                      {
                          if ( discount_on_particular > 0 )
                          {
                            discount_ids = tmp_discount_ids;
                            var forced_add_discount = confirm("If you add this discount on total fees, your other discount on particular will be removed, do you wish to continue?");
                          }
                      }
                  }
             }
             else
             {
                  var has_repeat_discount = 0;
                  j(".discount_category").each(function(){
                      var discount_particular_category_id = this.value;

                      if ( parseInt(discount_particular_category_id) == parseInt(discount_on) )
                      {
                          if ( parseInt(j(this).data('is-onetime')) == 0 )
                          {
                              var discount_id = this.id.replace("discount_category_","");
                              discount_ids = discount_id + ",";
                              has_repeat_discount++;
                          }
                      }
                  });

                  if ( discount_ids.length > 0 )
                  {
                      discount_ids = discount_ids.substr(0, discount_ids.length - 1);
                  }

                  if ( has_repeat_discount > 0 )
                  {
                      var forced_add_discount = confirm("Your previous discount on this particular will be overwrite by this discount, do you wish to continue?");
                  }
                  else
                  {
                      var feeTotalAmount = parseFloat(j("#fee_total_amount").val());
                      var discountAmount = discount_amount + parseFloat(j("#discount_total_amount").val());
                      if ( discountAmount > feeTotalAmount )
                      {
                          alert("Discount Amount can't be greater than fee amount");
                          return ;
                      }
                      else
                      {
                          var disabled_particular = false;
                          var fnd = false;
                          j(".fee_category").each(function(){
                              var particular_particular_category_id = this.value;
                              if ( parseInt(this.value) == parseInt(discount_on) )
                              {
                                  var fee_id = this.id.replace("fee_category_","");
                                  if (j("#fee_amount_particular_" + fee_id).hasClass("disabled"))
                                  {
                                      disabled_particular = true;
                                  }
                                  if ( ! fnd )
                                  {
                                    var fee_amount = parseFloat(j("#fee_particular_amount_" + fee_id).val());
                                    var dis_amount = 0;
                                    j(".discount_category").each(function(){
                                      var discount_particular_category_id = this.value;
                                      var discount_id = this.id.replace("discount_category_","");

                                      if ( discount_particular_category_id == particular_particular_category_id )
                                      {
                                        dis_amount += parseFloat(j("#fee_discount_amount_" + discount_id).val());
                                      }
                                    });

                                    var discountAmount = discount_amount + dis_amount;
                                    if ( discountAmount > fee_amount )
                                    {
                                        fnd = true;
                                    }
                                  }
                              }
                          });
                          if ( disabled_particular )
                          {
                              alert("You can't assign discount to this particular as this particular is disabled");
                              return ;
                          }
                          if ( fnd )
                          {
                              alert("Discount Amount can't be greater than particular fee amount");
                              return ;
                          }
                      }

                      if ( discount_on_total_fees > 0 )
                      {
                        alert("Sorry you can't add this discount on this particular. You must remove the discount on total fees first");
                        return ;
                      }
                  }
             }

             if ( forced_add_discount )
             {
                var html = '<tr id="discount_tr_id"></tr>';
                j('.discount_tr').last().after(html);
                j('#remove-extra-discount').removeClass("fa-minus-circle");
                j('#remove-extra-discount').addClass("fa-spinner");
                j('#remove-extra-discount').addClass("fa-spin");
                j('#remove-extra-discount').attr("id", "remove-extra-discount-spin");
                //alert(discount_name)
                new Ajax.Request('/finance/create_fees_with_tmp_discount', 
                     {
                       asynchronous:true, 
                       evalScripts:true, 
                       parameters:'amount='+discount_amount+'&discount_ids='+discount_ids+'&discount_on='+discount_on+'&batch_id='+j("#batch_id_particular").val()+'&date='+j("#date_id_particular").val()+'&student='+j("#student_id_particular").val()+'&discount_name='+discount_name+'&no_vat=1'+'&student_fees='+j("#is_student_fees").val()
                     }
                );
             }
        });

        j(document).on('click','.assign_vat_to_user', function(){
             var particular_name = "VAT";
             var particular_amount = parseFloat(j.trim(j("#fee_vat_amount").val()));
             if ( particular_amount == 0 )
             {
                alert("Particular amount Can't be Zero");
                return ;
             }

            new Ajax.Request('/finance/create_fees_with_tmp_particular', 
                  {
                    asynchronous:true, 
                    evalScripts:true, 
                    parameters:'amount='+particular_amount+'&batch_id='+j("#batch_id_particular").val()+'&date='+j("#date_id_particular").val()+'&student='+j("#student_id_particular").val()+'&particular='+particular_name+'&student_fees='+j("#is_student_fees").val()
                  }
             );
        });

        j(document).on("click",".chk_discount",function(){
              var id = this.id.replace('fee_discount_chk_','');
              if ( checkValidDiscount(id) )
              {
                  j(this).hide();
                  j("#fee_discount_amount_" + id).attr('type','hidden');
                  j("#fee_amount_discount_" + id).show();
                  j("#fee_amount_discount_" + id + " span").html(parseFloat(j("#fee_discount_amount_" + id).val()).toFixed(2));
                  calculateDiscount();
              }
              else
              {
                  alert("Invalid Discount Amount");
              }
        });
    });
}

function show_inactive_batches(){
    $('fees_submission_batch_id').value=""
    j('#hide2').show();
    j('#hide1').hide();
    j('#active-batch-link').show();
    j('#inactive-batch-link').hide();
}

function show_active_batches(){
    $('fees_submission_inactive_batch_id').value=""
    j('#hide1').show();
    j('#hide2').hide();
    j('#inactive-batch-link').show();
    j('#active-batch-link').hide();
}

function validate_payment_mode()
{
    if ($('payment')!=null)
    {
        if ($('payment').select('input')[0].value=="")
        {
          alert("Please select one payment mode");
          return false;
        }
        else
        {
          return true;
        }
    }
    else
    {
      return true;
    }
}

function validate_fine()
{
    if(isNaN($('fine_fee').value)==false)
    {
        if($('fine_fee').value <= 0)
        {
          $('fine_fee').value=""
          alert("Please enter a positive value for fine");
          return false;
        }
        else if($('fine_fee').value=="")
        {
          alert("Please enter a positive value for fine");
          return false;
        }
        else
        {
          return true;
        }
    }
    else
    {
        $('fine_fee').value=""
        alert("Please enter a numeric value for fine");
        return false;
    }
}

function validate_vat()
{
    if(isNaN($('vat_fee').value)==false)
    {
        if($('vat_fee').value <= 0)
        {
          $('fine_fee').value=""
          alert("Please enter a positive value for VAT");
          return false;
        }
        else if($('vat_fee').value=="")
        {
          alert("Please enter a positive value for VAT");
          return false;
        }
        else
        {
          return true;
        }
    }
    else
    {
        $('vat_fee').value=""
        alert("Please enter a numeric value for VAT");
        return false;
    }
}

function prev_double(){
    $('fees_form').setAttribute("onsubmit", "return false")
    $('submit_button').disable();
}

function set_back(){
    $('fees_form').removeAttr("onsubmit");
    setTimeout(function(){$('submit_button').enable();},15000);
}

loadJS();