<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * ZH-Revision: 09.Nov.2012
 */
return array(
    // Zend_I18n_Validator_Alnum
    "Invalid type given. String, integer or float expected" => "������һ��������С��",
    "The input contains characters which are non alphabetic and no digits" => "���벻��Ϊ��ĸ����������ַ�",
    "The input is an empty string" => "���벻��Ϊ��",

    // Zend_I18n_Validator_Alpha
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",
    "The input contains non alphabetic characters" => "���벻��Ϊ��ĸ������ַ�",
    "The input is an empty string" => "���벻��Ϊ��",

    // Zend_I18n_Validator_Float
    "Invalid type given. String, integer or float expected" => "��������һ��������С��",
    "The input does not appear to be a float" => "������Ч��������һ��С��",

    // Zend_I18n_Validator_Int
    "Invalid type given. String or integer expected" => "������Ч���������ַ�������",
    "The input does not appear to be an integer" => "������һ������",

    // Zend_I18n_Validator_PostCode
    "Invalid type given. String or integer expected" => "������Ч��������һ���ַ�������",
    "The input does not appear to be a postal code" => "��Ч�����������ʽ",
    "An exception has been raised while validating the input" => "��֤����ʱ���쳣����",

    // Zend_Validator_Barcode
    "The input failed checksum validation" => "����������޷�ͨ��У��",
    "The input contains invalid characters" => "��������������Ч���ַ�",
    "The input should have a length of %length% characters" => "��������볤��ӦΪ%length%���ַ�",
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",

    // Zend_Validator_Between
    "The input is not between '%min%' and '%max%', inclusively" => "��������ڵ���'%min%'��С�ڵ���'%max%'��ֵ",
    "The input is not strictly between '%min%' and '%max%'" => "���������'%min%'��С��'%max%'��ֵ",

    // Zend_Validator_Callback
    "The input is not valid" => "������Ч",
    "An exception has been raised within the callback" => "�ص������쳣����",

    // Zend_Validator_CreditCard
    "The input seems to contain an invalid checksum" => "����Ŀ��Ÿ�ʽ����",
    "The input must contain only digits" => "����ӦΪ����",
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",
    "The input contains an invalid amount of digits" => "����Ŀ��ų�������",
    "The input is not from an allowed institute" => "����Ŀ���û���ҵ���Ӧ�ķ��л���",
    "The input seems to be an invalid creditcard number" => "����Ŀ����޷�ͨ��У��",
    "An exception has been raised while validating the input" => "��֤����ʱ���쳣����",

    // Zend_Validator_Csrf
    "The form submitted did not originate from the expected site" => "���ύ��Դ��վδ�������",

    // Zend_Validator_Date
    "Invalid type given. String, integer, array or DateTime expected" => "������Ч���������ַ����ֻ�����",
    "The input does not appear to be a valid date" => "��������ڸ�ʽ��Ч",
    "The input does not fit the date format '%format%'" => "�밴�����ڸ�ʽ'%format%'����һ������",

    // Zend_Validator_DateStep
    "Invalid type given. String, integer, array or DateTime expected" => "������Ч���������ַ����ֻ�����",
    "The input does not appear to be a valid date" => "��������ڸ�ʽ��Ч",
    "The input is not a valid step" => "The input is not a valid step",

    // Zend_Validator_Db_AbstractDb
    "No record matching the input was found" => "û���ҵ�ƥ������ļ�¼",
    "A record matching the input was found" => "�����Ѿ���ռ��",

    // Zend_Validator_Digits
    "The input must contain only digits" => "���벻��Ϊ����������ַ�",
    "The input is an empty string" => "���벻��Ϊ��",
    "Invalid type given. String, integer or float expected" => "������Ч���������ַ�������С��",

    // Zend_Validator_EmailAddress
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "�����ʼ���ַ��ʽ���������ʽ�Ƿ�Ϊlocal-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%'����һ�����õ��ʼ�����",
    "'%hostname%' does not appear to have any valid MX or A records for the email address" => "'%hostname%'������û���ҵ����õ�MX��A��¼���ʼ��޷�Ͷ��",
    "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network" => "'%hostname%'�������������޷���·�ɣ��ʼ���ַӦλ�ڹ�������",
    "'%localPart%' can not be matched against dot-atom format" => "�ʼ��û�������'%localPart%'��ʽ�޷�ƥ��dot-atom��ʽ",
    "'%localPart%' can not be matched against quoted-string format" => "�ʼ��û�������'%localPart%'��ʽ�޷�ƥ��quoted-string��ʽ",
    "'%localPart%' is not a valid local part for the email address" => "'%localPart%'����һ����Ч���ʼ��û���",
    "The input exceeds the allowed length" => "���볬��������",

    // Zend_Validator_Explode
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",

    // Zend_Validator_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "�ļ����࣬�������'%max%'���ļ����ҵ�'%count%'��",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "�ļ����٣�������Ҫ'%min%'���ļ����ҵ�'%count%'��",

    // Zend_Validator_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "�ļ�'%value%'�޷�ͨ��CRC32У��",
    "A crc32 hash could not be evaluated for the given file" => "�ļ��޷�����CRC32У����",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_ExcludeExtension
    "File '%value%' has a false extension" => "�ļ�'%value%'��չ��������",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_Exists
    "File '%value%' does not exist" => "�ļ�'%value%'������",

    // Zend_Validator_File_Extension
    "File '%value%' has a false extension" => "�ļ�'%value%'��չ��������",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "�����ļ��ܴ�С'%size%'�������������'%max%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "�����ļ��ܴ�С'%size%'���㣬������Ҫ'%min%'",
    "One or more files can not be read" => "һ�������ļ��޷���ȡ",

    // Zend_Validator_File_Hash
    "File '%value%' does not match the given hashes" => "�ļ�'%value%'�޷�ͨ����ϣУ��",
    "A hash could not be evaluated for the given file" => "�ļ��޷����ɹ�ϣУ����",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "ͼƬ'%value%'�Ŀ��'%width%'�������������'%maxwidth%'",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "ͼƬ'%value%'�Ŀ��'%width%'���㣬����ӦΪ'%minwidth%'",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "ͼƬ'%value%'�ĸ߶�'%height%'�������������'%maxheight%'",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "ͼƬ'%value%'�ĸ߶�'%height%'���㣬����ӦΪ'%minheight%'",
    "The size of image '%value%' could not be detected" => "ͼƬ'%value%'�ĳߴ��޷���ȡ",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "�ļ�'%value%'û�б�ѹ������⵽�ļ���ý������Ϊ'%type%'",
    "The mimetype of file '%value%' could not be detected" => "�ļ�'%value%'��ý�������޷����",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "�ļ�'%value%'����ͼƬ����⵽�ļ���ý������Ϊ'%type%'",
    "The mimetype of file '%value%' could not be detected" => "�ļ�'%value%'��ý�������޷����",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_Md5
    "File '%value%' does not match the given md5 hashes" => "�ļ�'%value%'�޷�ͨ��MD5У��",
    "A md5 hash could not be evaluated for the given file" => "�ļ��޷�����MD5У����",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_MimeType
    "File '%value%' has a false mimetype of '%type%'" => "�ļ�'%value%'��ý������'%type%'������",
    "The mimetype of file '%value%' could not be detected" => "�ļ�'%value%'��ý�������޷����",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_NotExists
    "File '%value%' exists" => "�ļ�'%value%'�Ѿ�����",

    // Zend_Validator_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "�ļ�'%value%'�޷�ͨ��SHA1У��",
    "A sha1 hash could not be evaluated for the given file" => "�ļ��޷�����SHA1У����",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "�ļ�'%value%'�Ĵ�С'%size%'�������������'%max%'",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "�ļ�'%value%'�Ĵ�С'%size%'���㣬������Ҫ'%min%'",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_File_Upload
    "File '%value%' exceeds the defined ini size" => "�ļ�'%value%'��С����ϵͳ����Χ",
    "File '%value%' exceeds the defined form size" => "�ļ�'%value%'��С����������Χ",
    "File '%value%' was only partially uploaded" => "�ļ�'%value%'�ϴ�������",
    "File '%value%' was not uploaded" => "�ļ�'%value%'û�б��ϴ�",
    "No temporary directory was found for file '%value%'" => "û���ҵ���ʱ�ļ��д���ļ�'%value%'",
    "File '%value%' can't be written" => "�ļ�'%value%'�޷���д��",
    "A PHP extension returned an error while uploading the file '%value%'" => "�ļ�'%value%'�ϴ�ʱ������һ��PHP��չ����",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "�ļ�'%value%'���Ƿ��ϴ�������ܱ��ж�Ϊһ������",
    "File '%value%' was not found" => "�ļ�'%value%'������",
    "Unknown error while uploading file '%value%'" => "�ļ�'%value%'�ϴ�ʱ������һ��δ֪����",

    // Zend_Validator_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "����ĵ��ʹ��࣬�������'%max%'�����ʣ�������'%count%'��",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "����ĵ��ʹ��٣�������Ҫ'%min%'�����ʣ�������'%count%'��",
    "File '%value%' is not readable or does not exist" => "�ļ�'%value%'�޷���ȡ�򲻴���",

    // Zend_Validator_GreaterThan
    "The input is not greater than '%min%'" => "����Ӧ����'%min%'",
    "The input is not greater or equal than '%min%'" => "����Ӧ���ڵ���'%min%'",

    // Zend_Validator_Hex
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",
    "The input contains non-hexadecimal characters" => "������ʮ������������ַ�",

    // Zend_Validator_Hostname
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "�����DNS�����ڽ������޷��ø�����punycode��ȷ����",
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "�����DNS���������ӷ�λ�ò����Ϲ涨",
    "The input does not match the expected structure for a DNS hostname" => "�����DNS�����ṹ�������",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "�����DNS�����Ķ�������'%tld%'�޷�������",
    "The input does not appear to be a valid local network name" => "������������һ����������",
    "The input does not appear to be a valid URI hostname" => "������ʽ����",
    "The input appears to be an IP address, but IP addresses are not allowed" => "����������IP��ַ��Ϊ����",
    "The input appears to be a local network name but local network names are not allowed" => "���������뱾�ػ������������",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "�������DNS�������޷��ҵ�������������",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "�������DNS�����У��������������޷�ƥ����֪�б�",

    // Zend_Validator_Iban
    "Unknown country within the IBAN" => "�����IBAN�ʺ��޷��ҵ���Ӧ�Ĺ���",
    "Countries outside the Single Euro Payments Area (SEPA) are not supported" => "��֧�ֵ�һŷԪ֧����(SEPA)������ʺ�",
    "The input has a false IBAN format" => "�����IBAN�ʺŸ�ʽ����",
    "The input has failed the IBAN check" => "�����IBAN�ʺ�У��ʧ��",

    // Zend_Validator_Identical
    "The two given tokens do not match" => "������֤���Ʋ�ƥ��",
    "No token was provided to match against" => "û���������룬�޷�ƥ��",

    // Zend_Validator_InArray
    "The input was not found in the haystack" => "����û����ָ��������Χ��",

    // Zend_Validator_Ip
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",
    "The input does not appear to be a valid IP address" => "�����IP��ַ��ʽ����ȷ",

    // Zend_Validator_Isbn
    "Invalid type given. String or integer expected" => "������Ч���������ַ�������",
    "The input is not a valid ISBN number" => "�����ISBN��Ÿ�ʽ����ȷ",

    // Zend_Validator_LessThan
    "The input is not less than '%max%'" => "����ӦС��'%max%'",
    "The input is not less or equal than '%max%'" => "����ӦС�ڵ���'%max%'",

    // Zend_Validator_NotEmpty
    "Value is required and can't be empty" => "���벻��Ϊ��",
    "Invalid type given. String, integer, float, boolean or array expected" => "������Ч��ֻ�����ַ���������С��������ֵ����������",

    // Zend_Validator_Regex
    "Invalid type given. String, integer or float expected" => "������Ч���������ַ���������С��",
    "The input does not match against pattern '%pattern%'" => "���벻ƥ��ָ����ģʽ'%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "ƥ��ָ��ģʽ'%pattern%'ʱ���ڲ�������",

    // Zend_Validator_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "���벻������վ��ͼ��changefreq��ʽ",
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",

    // Zend_Validator_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "���벻������վ��ͼ��lastmod��ʽ",
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",

    // Zend_Validator_Sitemap_Loc
    "The input is not a valid sitemap location" => "���벻������վ��ͼ��location��ʽ",
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",

    // Zend_Validator_Sitemap_Priority
    "The input is not a valid sitemap priority" => "���벻������վ��ͼ��priority��ʽ",
    "Invalid type given. Numeric string, integer or float expected" => "������Ч��������һ������",

    // Zend_Validator_Step
    "Invalid value given. Scalar expected" => "������Ч��������һ������",
    "The input is not a valid step" => "���벻�ڽ��ݼ���Ľ����Χ��",

    // Zend_Validator_StringLength
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",
    "The input is less than %min% characters long" => "�����ַ�����Ӧ����%min%",
    "The input is more than %max% characters long" => "�����ַ�����ӦС��%max%",

    // Zend_Validator_Uri
    "Invalid type given. String expected" => "������Ч��������һ���ַ���",
    "The input does not appear to be a valid Uri" => "�����Uri��ʽ����",

	//Controller action
	'Index' => '��ҳ',
);
