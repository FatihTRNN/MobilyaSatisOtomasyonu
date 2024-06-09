using System;
using System.Data;
using System.Windows.Forms;
using MySql.Data.MySqlClient;

namespace MobilyaSatisOtomasyonu
{
    public class UrunServisi
    {
        private MySqlConnection conn;

        public UrunServisi(MySqlConnection connection)
        {
            conn = connection;
        }

        public DataTable Listele()
        {
            DataTable dt = new DataTable();
            try
            {
                conn.Open();
                string query = "SELECT urunID, urunAd, stok, alisFiyat, satisFiyat, kar, resim FROM urunler WHERE durum = true";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                MySqlDataAdapter da = new MySqlDataAdapter(cmd);
                da.Fill(dt);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Veritabanına bağlanırken hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
            return dt;
        }

        public void Kaydet(string ad, int stok, decimal alisFiyat, decimal satisFiyat, string resim)
        {
            try
            {
                conn.Open();
                decimal kar = satisFiyat - alisFiyat;
                string query = "INSERT INTO urunler (urunAd, stok, alisFiyat, satisFiyat, kar, durum, resim) VALUES (@ad, @stok, @alisFiyat, @satisFiyat, @kar, true, @resim)";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                cmd.Parameters.AddWithValue("@ad", ad);
                cmd.Parameters.AddWithValue("@stok", stok);
                cmd.Parameters.AddWithValue("@alisFiyat", alisFiyat);
                cmd.Parameters.AddWithValue("@satisFiyat", satisFiyat);
                cmd.Parameters.AddWithValue("@kar", kar);
                cmd.Parameters.AddWithValue("@resim", resim);
                cmd.ExecuteNonQuery(); // sql komutlarını çalıştıran kod.
            }
            catch (Exception ex)
            {
                Console.WriteLine("Hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
        }

        public void UrunGuncelle(int urunID, string urunAd, int stok, decimal alisFiyat, decimal satisFiyat, string resimYolu)
        {
            try
            {
                if (conn.State == ConnectionState.Closed)
                    conn.Open();

                decimal kar = satisFiyat - alisFiyat;
                string query = "UPDATE urunler SET urunAd = @urunAd, stok = @stok, alisFiyat = @alisFiyat, satisFiyat = @satisFiyat, kar = @kar, resim = @resim WHERE urunID = @urunID";
                using (MySqlCommand cmd = new MySqlCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@urunAd", urunAd);
                    cmd.Parameters.AddWithValue("@stok", stok);
                    cmd.Parameters.AddWithValue("@alisFiyat", alisFiyat);
                    cmd.Parameters.AddWithValue("@satisFiyat", satisFiyat);
                    cmd.Parameters.AddWithValue("@kar", kar);
                    cmd.Parameters.AddWithValue("@resim", resimYolu);
                    cmd.Parameters.AddWithValue("@urunID", urunID);
                    cmd.ExecuteNonQuery();
                }

                MessageBox.Show("Ürün başarıyla güncellendi.");
            }
            catch (Exception ex)
            {
                MessageBox.Show("Bir hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
        }

        public void Sil(int id)
        {
            try
            {
                conn.Open();
                string query = "DELETE FROM urunler WHERE urunID = @id";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                cmd.Parameters.AddWithValue("@id", id);
                cmd.ExecuteNonQuery();
            }
            catch (Exception ex)
            {
                Console.WriteLine("Hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
        }

        public DataTable Bul(string urunAd)
        {
            DataTable dt = new DataTable();
            try
            {
                conn.Open();
                string query = "SELECT urunID, urunAd, stok, alisFiyat, satisFiyat, kar, resim FROM urunler WHERE urunAd LIKE @urunAd AND durum = true";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                cmd.Parameters.AddWithValue("@urunAd", "%" + urunAd + "%");
                MySqlDataAdapter da = new MySqlDataAdapter(cmd);
                da.Fill(dt);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
            return dt;
        }
    }
}
