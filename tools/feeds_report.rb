#!/usr/bin/env ruby

require "rubygems"
require 'feed_validator'
require File.join File.dirname(__FILE__), "planetalinux"

require "pp"

PlanetaLinux.instances.each do |instance|
	puts "#{instance}:"
	PlanetaLinux.feeds_by_instance(instance).each_pair do |k, v|
		v.each do |f|
      # puts "trying with #{f}"
      val = W3C::FeedValidator.new
      begin
        val.validate_url f
        unless val.valid?
          puts " #{f} seems to be an invalid feed and it's currently #{k}"
        end
      rescue REXML::ParseException
        puts " #{f} - timeout?"
      end
		end
	end
end

