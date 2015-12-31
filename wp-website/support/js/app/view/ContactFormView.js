//==============================================================================
//
//  Contact form view
//
//==============================================================================

(function(app, $)
{
    app.ContactFormView = Backbone.View.extend({
    
        mailExp : new RegExp('^[-+\\.0-9=a-z_]+@([-0-9a-z]+\\.)+([0-9a-z]){2,}$', 'i'),
        timeExp : new RegExp ('([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])','i'),
        
        nameValid    : false,
        mailValid    : false,
        messageValid : false,
        schoolValid  : false,
        startValid   : false,
        endValid     : false,
        
        initialize : function()
        {
            // Cache view elements
            
            this.$name                  = this.$('#customer-chat-contact-name');
            this.$mail                  = this.$('#customer-chat-contact-mail');
            this.$message               = this.$('#customer-chat-contact-message');
            this.$contactSchool         = this.$('#customer-chat-contact-school-name');
            this.$contactStartTime      = this.$('#customer-chat-contact-preferred-time-start');
            this.$contactEndTime        = this.$('#customer-chat-contact-preferred-time-end');
            
            this.$name.on   ('input change keydown blur', $.proxy(this.validateName,    this));
            this.$mail.on   ('input change keydown blur', $.proxy(this.validateMail,    this));
            this.$message.on('input change keydown blur', $.proxy(this.validateMessage, this));
        },
        
        reset : function()
        {
            this.$name.val('');
            this.$mail.val('');
            this.$message.val('');
            this.$contactSchool.val('');
            this.$contactStartTime.val('');
            this.$contactEndTime.val('');
            
            this.$name.removeClass   ('customer-chat-input-error');
            this.$mail.removeClass   ('customer-chat-input-error');
            this.$message.removeClass('customer-chat-input-error');
            this.$contactSchool.removeClass   ('customer-chat-input-error');
            this.$contactStartTime.removeClass   ('customer-chat-input-error');
            this.$contactEndTime.removeClass('customer-chat-input-error');
        },
        
        validateSchool : function()
        {
            if(this.$contactSchool.val().length == 0)
            {
                this.$contactSchool.addClass('customer-chat-input-error');
                
                this.schoolValid = false;
            }
            else
            {
                this.$contactSchool.removeClass('customer-chat-input-error');
                
                this.schoolValid = true;
            }
        },
        
        validateName : function()
        {
            if(this.$name.val().length == 0)
            {
                this.$name.addClass('customer-chat-input-error');
                
                this.nameValid = false;
            }
            else
            {
                this.$name.removeClass('customer-chat-input-error');
                
                this.nameValid = true;
            }
        },
        
        validateMail : function()
        {
            if(this.$mail.val().length == 0 || !this.mailExp.test(this.$mail.val()))
            {
                this.$mail.addClass('customer-chat-input-error');
                
                this.mailValid = false;
            }
            else
            {
                this.$mail.removeClass('customer-chat-input-error');
                
                this.mailValid = true;
            }
        },
        validateStart : function()
        {
            if(this.$contactStartTime.val().length == 0 || !this.timeExp.test(this.$contactStartTime.val()))
            {
                this.$contactStartTime.addClass('customer-chat-input-error');
                
                this.startValid = false;
            }
            else
            {
                this.$contactStartTime.removeClass('customer-chat-input-error');
                
                this.startValid = true;
            }
        },
        
        validateEnd : function()
        {
            if(this.$contactEndTime.val().length == 0 || !this.timeExp.test(this.$contactEndTime.val()))
            {
                this.$contactEndTime.addClass('customer-chat-input-error');
                
                this.endValid = false;
            }
            else
            {
                this.$contactEndTime.removeClass('customer-chat-input-error');
                
                this.endValid = true;
            }
        },
        
        validateMessage : function()
        {
            if(this.$message.val().length < 6)
            {
                this.$message.addClass('customer-chat-input-error');
                
                this.messageValid = false;
            }
            else
            {
                this.$message.removeClass('customer-chat-input-error');
                
                this.messageValid = true;
            }
        },
        
        isValid : function()
        {
            this.validateName();
            this.validateMail();
            this.validateMessage();
            this.validateSchool();
            this.validateStart();
            this.validateEnd();
            
            return this.nameValid && this.mailValid && this.messageValid && this.schoolValid && this.startValid && this.endValid;
        }
    });

})(window.Application, jQuery);