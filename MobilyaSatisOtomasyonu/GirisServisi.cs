using System;
using System.Data;
using MySql.Data.MySqlClient;

namespace MobilyaSatisOtomasyonu
{
    public class GirisServisi
    {
        public bool KullaniciGiris(string kullaniciAdi, string parola)
        {
            if (kullaniciAdi == "sinopmobilya" && parola == "5757")
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
}