#!/usr/bin/ruby

module PlanetaLinux
	def PlanetaLinux.get_all
		all = String.new
		str = Dir["#{File.expand_path(File.dirname(__FILE__))}/../proc/*/config.ini"].each do |i|
			next if File.directory?(i)

			i =~ /proc\/(.+?)\/config\.ini$/i
			all << self.get_subs(i, $1)
		end
		all

	end

	def PlanetaLinux.get_subs(i, instance)
		str = String.new
		File.new(i).read.scan(/^\[(http.+?)\]\nname ?= ?(.+?)$/).each do |sub|
			str << %Q|[#{sub[0]}]
name = (#{instance}) #{sub[1]}

|
		end
		str
	end

end

class PlanetaLinuxUniverso
	@@universo_template = <<EOF
# Éste es un template creado automáticamente, favor de no modificat
# los feeds aquí especificados. Para agregarse feeds, éstos tienen
# que ser agregados en cada una de las instancias en los países

[Planet]

name = Universo Planeta Linux
link = http://universo.planetalinux.org/
owner_name = Planeta Linux
owner_email = planetalinux@googlegroups.com
country_tld = universo
country = Universo

cache_directory = /tmp/planetalinux
new_feed_items = 10
log_level = DEBUG

template_files = /home/planetalinux/current/proc/rss20-new.xml.tmpl
output_dir = /home/planetalinux/www/universo.planetalinux.org

items_per_page = 60
days_per_page = 7
date_format = %l:%M %P
new_date_format = %d de %B de %Y
encoding = utf-8
locale = es_MX.UTF-8

[DEFAULT]
face = nobody.png

EOF

	def initialize
	end

	def dump
		@@universo_template << PlanetaLinux.get_all
	end

end

universo = PlanetaLinuxUniverso.new
File.open("#{File.dirname(__FILE__)}/../proc/universo/config.ini", "w") do |f|
	f.write(universo.dump)
end


