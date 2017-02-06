class AddSchoolIdToClassworks < ActiveRecord::Migration
  def self.up
    [:Classworks,:Classwork_answers].each do |c|
      add_column c,:school_id,:integer
      add_index c,:school_id
    end    
  end

  def self.down
    [:Classworks,:Classwork_answers].each do |c|
      remove_index c,:school_id
      remove_column c,:school_id
    end
  end
end
