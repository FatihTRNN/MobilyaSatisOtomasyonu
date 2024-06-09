using MySql.Data.MySqlClient;
using System;
using System.Data;
using System.Windows.Forms;

namespace MobilyaSatisOtomasyonu
{
    public class SatisServisi
    {
        private MySqlConnection conn;

        public SatisServisi(MySqlConnection connection)
        {
            conn = connection;
        }

        public DataTable ListeleSatislar()
        {
            DataTable dt = new DataTable();
            try
            {
                if (conn.State == ConnectionState.Closed)
                    conn.Open();

                string query = "CALL satislistesi_goruntule()"; // prosedür çağırma ürün id müşteri idden ürün ad ve müşteri ad birleşimi için
                MySqlCommand cmd = new MySqlCommand(query, conn);
                MySqlDataAdapter da = new MySqlDataAdapter(cmd);
                da.Fill(dt);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Veritabanı bağlantısı kurulurken hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
            return dt;
        }

        public DataTable ListeleUrunler()
        {
            DataTable dt = new DataTable();
            try
            {
                if (conn.State == ConnectionState.Closed)
                    conn.Open();

                string query = "SELECT urunID, urunAd FROM urunler WHERE durum = true AND stok > 0";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                MySqlDataAdapter da = new MySqlDataAdapter(cmd);
                da.Fill(dt);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Veritabanı bağlantısı kurulurken hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
            return dt;
        }

        public DataTable ListeleMusteriler()
        {
            DataTable dt = new DataTable();
            try
            {
                if (conn.State == ConnectionState.Closed)
                    conn.Open();

                string query = "SELECT musteriID, CONCAT(ad, ' ', soyad) AS adSoyad FROM musteriler WHERE durum = true";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                MySqlDataAdapter da = new MySqlDataAdapter(cmd);
                da.Fill(dt);
            }
            catch (Exception ex)
            {
                Console.WriteLine("Veritabanı bağlantısı kurulurken hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
            return dt;
        }

        public decimal HesaplaFiyat(int urunID, int adet)
        {
            decimal toplamFiyat = 0;
            try
            {
                if (conn.State == ConnectionState.Closed)
                    conn.Open();

                string query = "SELECT stok, satisFiyat FROM urunler WHERE urunID = @urunID";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                cmd.Parameters.AddWithValue("@urunID", urunID);
                MySqlDataReader reader = cmd.ExecuteReader();

                if (reader.Read())
                {
                    int stok = reader.GetInt32("stok");
                    decimal satisFiyat = reader.GetDecimal("satisFiyat");

                    if (stok < adet)
                    {
                        MessageBox.Show("Stoktaki ürün yetersiz ", "Hatalı İşlem", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    }
                    else
                    {
                        toplamFiyat = adet * satisFiyat;
                    }
                }
                reader.Close();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hata oluştu: " + ex.Message);
            }
            finally
            {
                conn.Close();
            }
            return toplamFiyat;
        }

        public void SatisYap(int urunID, int musteriID, int adet, decimal toplamFiyat)
        {
            try
            {
                if (conn.State == ConnectionState.Closed)
                    conn.Open();

                DateTime tarih = DateTime.Today;

                // Ürün adını al
                string urunAd = "";
                string urunAdQuery = "SELECT urunAd FROM urunler WHERE urunID = @urunID";
                using (MySqlCommand urunAdCmd = new MySqlCommand(urunAdQuery, conn))
                {
                    urunAdCmd.Parameters.AddWithValue("@urunID", urunID);
                    using (MySqlDataReader reader = urunAdCmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            urunAd = reader.GetString("urunAd");
                        }
                    }
                }

                // Önce var olan bir kayıt olup olmadığını kontrol etme
                string checkQuery = "SELECT COUNT(*) FROM satislistesi WHERE urunID = @urunID AND musteriID = @musteriID";
                using (MySqlCommand checkCmd = new MySqlCommand(checkQuery, conn))
                {
                    checkCmd.Parameters.AddWithValue("@urunID", urunID);
                    checkCmd.Parameters.AddWithValue("@musteriID", musteriID);
                    int count = Convert.ToInt32(checkCmd.ExecuteScalar());

                    if (count > 0)
                    {
                        // Var olan bir kayıt varsa, adet ve toplam fiyatı güncelle
                        string updateQuery = "UPDATE satislistesi SET adet = adet + @adet, toplamFiyat = toplamFiyat + @toplamFiyat WHERE urunID = @urunID AND musteriID = @musteriID";
                        using (MySqlCommand updateCmd = new MySqlCommand(updateQuery, conn))
                        {
                            updateCmd.Parameters.AddWithValue("@adet", adet);
                            updateCmd.Parameters.AddWithValue("@toplamFiyat", toplamFiyat);
                            updateCmd.Parameters.AddWithValue("@urunID", urunID);
                            updateCmd.Parameters.AddWithValue("@musteriID", musteriID);
                            updateCmd.ExecuteNonQuery();
                        }
                    }
                    else
                    {
                        // Yeni bir kayıt ekle
                        string insertQuery = "INSERT INTO satislistesi (urunID, urunAd, musteriID, adet, toplamFiyat, tarih) VALUES (@urunID, @urunAd, @musteriID, @adet, @toplamFiyat, @tarih)";
                        using (MySqlCommand insertCmd = new MySqlCommand(insertQuery, conn))
                        {
                            insertCmd.Parameters.AddWithValue("@urunID", urunID);
                            insertCmd.Parameters.AddWithValue("@urunAd", urunAd);
                            insertCmd.Parameters.AddWithValue("@musteriID", musteriID);
                            insertCmd.Parameters.AddWithValue("@adet", adet);
                            insertCmd.Parameters.AddWithValue("@toplamFiyat", toplamFiyat);
                            insertCmd.Parameters.AddWithValue("@tarih", tarih);
                            insertCmd.ExecuteNonQuery();
                        }
                    }
                }

                // Ürün stoklarını güncelle
                string updateStokQuery = "UPDATE urunler SET stok = stok - @adet WHERE urunID = @urunID";
                using (MySqlCommand updateStokCmd = new MySqlCommand(updateStokQuery, conn))
                {
                    updateStokCmd.Parameters.AddWithValue("@adet", adet);
                    updateStokCmd.Parameters.AddWithValue("@urunID", urunID);
                    updateStokCmd.ExecuteNonQuery();
                }

                MessageBox.Show("Satış yapıldı");
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hata oluştu: " + ex.Message);
                Console.WriteLine("Hata: " + ex.Message);
                if (ex.InnerException != null)
                {
                    Console.WriteLine("İç hata: " + ex.InnerException.Message);
                }
            }
            finally
            {
                conn.Close();
            }
        }




    }

}

