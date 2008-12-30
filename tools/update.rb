#!/usr/bin/ruby

Dir.chdir "#{ENV["HOME"]}/current"
exec "git pull origin master"

Dir.chdir "/var/www/planetalinux/git"
exec "git pull origin master"

Dir.chdir "#{ENV["HOME"]}/current/proc"

skip_dirs = %w/inc universo/

Dir["*/config.ini"].each do |ini|
	next if skip_dirs.include?(ini.split('/').first)
	
	puts "Processing: #{ini}"
	exec "planetplanet #{ini}"
end

