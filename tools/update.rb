#!/usr/bin/ruby

Dir.chdir "#{ENV["HOME"]}/current"
%x[git pull origin master]

Dir.chdir "/var/www/planetalinux/git"
%x[git pull origin master]

Dir.chdir "#{ENV["HOME"]}/current/proc"

skip_dirs = %w/inc universo/

threads = []

i = 1
Dir["*/config.ini"].sort.each do |ini|
        next if skip_dirs.include?(ini.split('/').first)

				threads << Thread.new {
	        puts "Processing: #{ini}"
					if ARGV[0] == "dry"
						puts " NOT!"
					else
		        %x[planetplanet #{ini}]
					end
				}
end


