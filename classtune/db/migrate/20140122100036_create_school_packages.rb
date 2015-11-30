class CreateSchoolPackages < ActiveRecord::Migration
  def self.up
    create_table :school_packages do |t|
      t.integer   :package_id
      t.integer   :school_id
      
      t.timestamps
    end
  end

  def self.down
    drop_table :school_packages
  end



end
