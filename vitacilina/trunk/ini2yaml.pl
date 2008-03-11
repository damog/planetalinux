#!/usr/bin/perl

#
# Para Planeta Linux
#  David Moreno
#

use strict;
use warnings;

binmode STDOUT, ":encoding(ISO-8859-1)";

use Config::Tiny;
use Data::Dumper;
use YAML::Syck;

die usage("No se especificó instancia") unless $ARGV[0];

my $ini = Config::Tiny->read("../../proc/$ARGV[0]/config.ini");
my $yaml = Dump($ini);

print $yaml;


sub usage {
	my($msg) = shift;

	print <<EOF;
$msg

Uso: $0 [ instancia ]
 (donde instancia es mx, ve, cl, etc.)
EOF
	exit;
}