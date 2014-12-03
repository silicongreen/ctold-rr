# Champs21FeeImport
require 'dispatcher'
module Champs21TallyExport
  def self.attach_overrides
    Dispatcher.to_prepare(:champs21_tally_export) do
      FinanceTransaction.instance_eval { include FinanceTransactionExtension }
      FinanceTransactionCategory.instance_eval { belongs_to :tally_ledger }
    end
  end

  module FinanceTransactionExtension
    def self.included(base)
      base.instance_eval do
        after_create :sync_with_tally
        has_one :tally_export_log
        belongs_to :tally_ledger 
      end
    end
    
    def sync_with_tally
      if Champs21Plugin.can_access_plugin?("champs21_tally_export")
        enable_live_sync = TallyExportConfiguration.get_config_value('EnableLiveSync')
        sync_start_date  = TallyExportConfiguration.get_config_value('LiveSyncStartDate') if enable_live_sync == '1'
        unless sync_start_date.blank?
          Delayed::Job.enqueue(TallyExportJob.new(self.id)) if sync_start_date.to_date <= Date.today
        end
      end
    end
  end
end

