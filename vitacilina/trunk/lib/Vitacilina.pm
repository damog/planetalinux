#!/usr/bin/perl

# Copyright (c) 2008 - Axiombox - http://www.axiombox.com/
#	David Moreno Garza <david@axiombox.com>
# 
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#

package Vitacilina;

use strict;
use warnings;

use URI;
use Template;
use XML::Feed;
use YAML::Syck;
use Data::Dumper;
use Carp;

use Vitacilina::Config qw/$FORMAT $OUTPUT/;

my $params = {
	required => [qw{config template}],
	optional => [qw/title format/],
};

sub new {
	my($self, %opts) = @_;
	
	my $o = \%opts;
	
	return bless {
		format			=> $opts{format} || $FORMAT,
		output			=> $opts{output} || $OUTPUT,
		config			=> $opts{config} || '',
		title 			=> $opts{title} || '',
		template		=> $opts{template} || '',
	}, $self;
}

sub config {
	my($self, $config) = @_;
	$self->{config} = $config if $config;
	$self->{config};
}

sub title {
	my($self, $title) = @_;
	$self->{title} = $title if $title;
	$self->{title};
}

sub template {
	my($self, $t) = @_;
	$self->{template} = $t if $t;
	$self->{template};
}

sub format {
	my($self, $f) = @_;
	$self->{format} = $f if $f;
	$self->{format};
}

sub output {
	my($self, $o) = shift;
	$self->{output} = $o if $o;
	$self->{output};
}

sub render {
	my($self) = shift;
	
	my $tt = Template->new(
		RELATIVE => 1
	);
	
	$tt->process(
		$self->template,
		{ data => $self->_data },
		$self->output
	) or die $tt->error;
	
}

sub _data {
	my($self) = shift;
	
	foreach (@{$params->{required}}) {
		croak "No $_ was defined" unless $self->{$_};
	}
	
	my $c = LoadFile($self->{config});
	
	my @entries;
	
	while(my($k, $v) = each %{$c}) {
		next if $k eq 'Planet' or $k eq 'DEFAULT';
		
		my $feed = XML::Feed->parse(URI->new($k));
		
		unless($feed) {
			print STDERR 
				'ERROR: ',
				XML::Feed->errstr, 
				': ',
				$k, "\n";
			next;
		};
		
		push @entries, map {
			{
				author => $v->{name},
				content => $_->content->body,
				title => $_->title,
				date => $_->issued,
				date_modified => $_->modified,
			}
		} $feed->entries;
		
	}
	
	my $zero = DateTime->from_epoch(epoch => 0);
		
	@entries = sort {
		($b->{date} || $b->{date_modified} || $zero)
		<=>
		($a->{date} || $b->{date_modified} || $zero)
	} @entries;
	
	return \@entries;
	
}

1;
