authorization do

#role :poll_control_basic do
#   has_permission_on [:poll_questions],
#      :to => [
#      :index,
#      :show,
#      :voting  ]
#end
#
#  role :poll_control  do
#    has_permission_on [:poll_questions],
#      :to => [
#      :index,
#      :new,
#      :show,
#      :edit,
#      :create,
#      :update,
#      :destroy,
#      :voting,
#      :close_poll,
#      :open_poll]
#  end

  # admin privileges
#  role :admin do
#  includes :poll_control
#  end
#
#  # employee -privileges
#  role :employee do
#  includes :poll_control_basic
#  end
#
#  role :student do
#  includes :poll_control_basic
#  end
end