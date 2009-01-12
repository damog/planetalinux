require File.dirname(__FILE__) + "/ini"
require "tempfile"
require "pp"

module PlanetaLinux

	def self.instances
		arr = []
		Dir["#{File.expand_path(File.dirname(__FILE__))}/../proc/*/config.ini"].each do |i|
			next if i =~ /universo\/config.ini/ 
			arr.push(i.split('/')[-2])
		end
		return arr
	end

	def self.feeds_by_instance(instance)
		feeds = {
			"active" => [],
			"inactive" => []
		}
		File.open("#{File.dirname(__FILE__)}/../proc/#{instance}/config.ini", "r").each do |line|
			next unless line =~ /\[http/

			line =~ /\[(.+?)\]/
			url = $1

			if line.lstrip =~ /^#/
				feeds["inactive"].push(url)
			else
				feeds["active"].push(url)
			end
		end
		feeds
	end

  def self.get_all
    all = String.new
    str = Dir["#{File.expand_path(File.dirname(__FILE__))}/../proc/*/config.ini"].each do |i|
      next if File.directory?(i) or i =~ /universo\/config.ini/

      i =~ /proc\/(.+?)\/config\.ini$/i
      all << self.get_subs(i, $1)
    end
    all

  end

  def self.get_subs(i, instance)
    ini = Ini.read_from_file(i)
    str = String.new
    ini.each_pair do |k, v|
      next if k == "DEFAULT" or k == "Planet"
      str << "[#{k}]\nname = (#{ini["Planet"]["name"].gsub('Planeta Linux ', '')}) #{ini[k]["name"]}\n"

      if ini[k].has_key?("face")
        str << "face = #{instance}/#{ini[k]["face"]}\n"
      end

      if ini[k].has_key?("portal")
        str << "portal = #{ini[k]["portal"]}\n"
      end
      str << "\n"
    end
    str
  end

	def self.universo
		all = self.get_all

		tmp = Tempfile.new('random')
		File.open(tmp.path, 'w') do |f|
		  f.write(all)
		end

		Ini.read_from_file(tmp.path)
	end
end

