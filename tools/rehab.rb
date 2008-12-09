#!/usr/bin/ruby

require "planetalinux"
require "rubygems"
require "rfeedparser"

universo = PlanetaLinux.universo

universo.each_pair do |k, v|
	puts "#{k}"
	fp = FeedParser.parse k
end

