
package ONVIF::Device::Elements::DeleteUsers;
use strict;
use warnings;

{ # BLOCK to scope variables

sub get_xmlns { 'http://www.onvif.org/ver10/device/wsdl' }

__PACKAGE__->__set_name('DeleteUsers');
__PACKAGE__->__set_nillable();
__PACKAGE__->__set_minOccurs();
__PACKAGE__->__set_maxOccurs();
__PACKAGE__->__set_ref();

use base qw(
    SOAP::WSDL::XSD::Typelib::Element
    SOAP::WSDL::XSD::Typelib::ComplexType
);

our $XML_ATTRIBUTE_CLASS;
undef $XML_ATTRIBUTE_CLASS;

sub __get_attr_class {
    return $XML_ATTRIBUTE_CLASS;
}

use Class::Std::Fast::Storable constructor => 'none';
use base qw(SOAP::WSDL::XSD::Typelib::ComplexType);

Class::Std::initialize();

{ # BLOCK to scope variables

my %Username_of :ATTR(:get<Username>);

__PACKAGE__->_factory(
    [ qw(        Username

    ) ],
    {
        'Username' => \%Username_of,
    },
    {
        'Username' => 'SOAP::WSDL::XSD::Typelib::Builtin::string',
    },
    {

        'Username' => 'Username',
    }
);

} # end BLOCK







} # end of BLOCK



1;


=pod

=head1 NAME

ONVIF::Device::Elements::DeleteUsers

=head1 DESCRIPTION

Perl data type class for the XML Schema defined element
DeleteUsers from the namespace http://www.onvif.org/ver10/device/wsdl.







=head1 PROPERTIES

The following properties may be accessed using get_PROPERTY / set_PROPERTY
methods:

=over

=item * Username

 $element->set_Username($data);
 $element->get_Username();





=back


=head1 METHODS

=head2 new

 my $element = ONVIF::Device::Elements::DeleteUsers->new($data);

Constructor. The following data structure may be passed to new():

 {
   Username =>  $some_value, # string
 },

=head1 AUTHOR

Generated by SOAP::WSDL

=cut
