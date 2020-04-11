#Copyright 2010 teamCreative Private Limited
#This product includes software developed at
#Project Champs21 - http://www.champs21.com/
#
#Licensed under the Apache License, Version 2.0 (the "License");
#you may not use this file except in compliance with the License.
#You may obtain a copy of the License at
#
#  http://www.apache.org/licenses/LICENSE-2.0
#
#Unless required by applicable law or agreed to in writing,
#software distributed under the License is distributed on an
#"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
#KIND, either express or implied.  See the License for the
#specific language governing permissions and limitations
#under the License.
module OnlineMeetingRoomsHelper
    def qrcode_url(content, size=nil)
      size ||= "200x200"
      content = CGI::escape(content)
      "https://chart.googleapis.com/chart?cht=qr&chs=#{size}&chl=#{content}&choe=UTF-8"
    end
    
    # Helper for converting BigBlueButton dates into a nice length string.
    def recording_length(playbacks)
      # Looping through playbacks array and returning first non-zero length value
      playbacks.each do |playback|
        length = playback[:length]
        return recording_length_string(length) unless length.zero?
      end
      # Return '< 1 min' if length values are zero
      "< 1 min"
    end
    
    private
    
    # Returns length of the recording as a string
    def recording_length_string(len)
      if len > 60
        "#{(len / 60).to_i} h #{len % 60} min"
      else
        "#{len} min"
      end
    end
end
