#!/usr/bin/env ruby

require "rubygems"
require "rfeedparser"
require "planetalinux"

require "pp"

PlanetaLinux.instances.each do |instance|
	puts "#{instance}:"
	PlanetaLinux.feeds_by_instance(instance).each_pair do |k, v|
		v.each do |f|
			Thread.new {
				begin
					fp = FeedParser.parse f
				rescue
					next
				end

				if k == "active"
					if fp.bozo
						puts " '#{f}' seems to be an invalid feed and it's currently active"
					end
				elsif k == "inactive"
					unless fp.bozo
						if fp.entries.size > 0
							puts " '#{f}' is currently inactive but appears to be valid and it has entries"
						else
							puts " '#{f}' is currently inactive but appears to be valid but without entries"
						end
					end
				else
					puts "BUG ON PLANETLINUX.RB!"
					exit
				end
			}
		end
	end
end

