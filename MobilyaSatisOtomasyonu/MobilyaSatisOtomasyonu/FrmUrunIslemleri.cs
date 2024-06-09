using System;
using System.Data;
using System.Drawing;
using System.IO;
using System.Windows.Forms;
using MySql.Data.MySqlClient;

namespace MobilyaSatisOtomasyonu
{
    public partial class FrmUrunIslemleri : Form
    {
        private MySqlConnection conn;
        private string connectionString = "Server=localhost;Port=3306;Database=mobilyasatis;Uid=root;Pwd=;";
        private UrunServisi urunServisi; // Class tanımı.

        public FrmUrunIslemleri()
        {
            InitializeComponent();
            conn = new MySqlConnection(connectionString);
            urunServisi = new UrunServisi(conn);
        }

        private void FrmUrunIslemleri_Load(object sender, EventArgs e)
        {
            Listele();
        }

        private void btnListele_Click(object sender, EventArgs e)
        {
            Listele();
        }

        private void btnKaydet_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(textBox1.Text) || string.IsNullOrWhiteSpace(txtAlisFiyat.Text) || string.IsNullOrWhiteSpace(txtSatisFiyat.Text) || string.IsNullOrWhiteSpace(txtFotoPath.Text))
            {
                MessageBox.Show("Lütfen tüm alanları doldurun", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            try
            {
                decimal alisFiyat = Convert.ToDecimal(txtAlisFiyat.Text);
                decimal satisFiyat = Convert.ToDecimal(txtSatisFiyat.Text);
                string resimYolu = SaveImage(txtFotoPath.Text);
                urunServisi.Kaydet(textBox1.Text, Convert.ToInt32(numStok.Value), alisFiyat, satisFiyat, resimYolu);

                MessageBox.Show("Yeni ürün kaydedildi");
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hata oluştu: " + ex.Message);
            }
            finally
            {
                Listele();
            }
        }

        private void btnSil_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(txtID.Text))
            {
                MessageBox.Show("Silmek istediğiniz kaydı seçiniz", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            try
            {
                int id = Convert.ToInt32(txtID.Text);
                urunServisi.Sil(id);
                MessageBox.Show("Ürün silindi");
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hata oluştu: " + ex.Message);
            }
            finally
            {
                Listele();
            }
        }

        private void dataGridView1_CellClick(object sender, DataGridViewCellEventArgs e)
        {
            if (e.RowIndex >= 0)
            {
                DataGridViewRow row = this.dataGridView1.Rows[e.RowIndex];

                txtID.Text = row.Cells["urunID"].Value != DBNull.Value ? row.Cells["urunID"].Value.ToString() : string.Empty;
                textBox1.Text = row.Cells["urunAd"].Value != DBNull.Value ? row.Cells["urunAd"].Value.ToString() : string.Empty;
                numStok.Value = row.Cells["stok"].Value != DBNull.Value ? Convert.ToInt32(row.Cells["stok"].Value) : 0;
                txtAlisFiyat.Text = row.Cells["alisFiyat"].Value != DBNull.Value ? row.Cells["alisFiyat"].Value.ToString() : string.Empty;
                txtSatisFiyat.Text = row.Cells["satisFiyat"].Value != DBNull.Value ? row.Cells["satisFiyat"].Value.ToString() : string.Empty;
                Kar.Text = row.Cells["kar"].Value != DBNull.Value ? "Elde Edilen Kâr: " + row.Cells["kar"].Value.ToString() : "Elde Edilen Kâr: 0";
                txtFotoPath.Text = row.Cells["resim"].Value != DBNull.Value ? row.Cells["resim"].Value.ToString() : string.Empty;

                // Seçilen ürünün fotoğrafını göster
                if (File.Exists(NormalizePath(row.Cells["resim"].Value.ToString())))
                {
                    try
                    {
                        using (FileStream fs = new FileStream(NormalizePath(row.Cells["resim"].Value.ToString()), FileMode.Open, FileAccess.Read))
                        {
                            pictureBox1.Image = Image.FromStream(fs);
                        }
                    }
                    catch (IOException ex)
                    {
                        MessageBox.Show("Fotoğraf yüklenemedi: " + ex.Message, "Hata", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    }
                }
                else
                {
                    pictureBox1.Image = null;
                }
            }
        }

        private void btnGuncelle_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(txtID.Text) || string.IsNullOrWhiteSpace(textBox1.Text) || string.IsNullOrWhiteSpace(txtAlisFiyat.Text) || string.IsNullOrWhiteSpace(txtSatisFiyat.Text))
            {
                MessageBox.Show("Lütfen tüm alanları doldurun", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            int urunID = int.Parse(txtID.Text);
            string urunAd = textBox1.Text;
            int stok = (int)numStok.Value;
            decimal alisFiyat = decimal.Parse(txtAlisFiyat.Text);
            decimal satisFiyat = decimal.Parse(txtSatisFiyat.Text);
            string resimYolu = txtFotoPath.Text;

            // Eğer yeni bir resim seçilmişse, resmi kaydet
            if (!string.IsNullOrWhiteSpace(txtFotoPath.Text) && File.Exists(txtFotoPath.Text))
            {
                resimYolu = SaveImage(txtFotoPath.Text);
            }

            try
            {
                urunServisi.UrunGuncelle(urunID, urunAd, stok, alisFiyat, satisFiyat, resimYolu);
                Listele();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Bir hata oluştu: " + ex.Message);
            }
        }

        private void btnBul_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(txtBul.Text))
            {
                MessageBox.Show("Lütfen aranacak kelimeyi girin", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            try
            {
                DataTable dt = urunServisi.Bul(txtBul.Text);
                dataGridView1.DataSource = dt;
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hata oluştu: " + ex.Message);
            }
        }

        private void btnFotoSec_Click(object sender, EventArgs e)
        {
            using (OpenFileDialog ofd = new OpenFileDialog())
            {
                ofd.Filter = "Image Files (*.jpg;*.jpeg;*.png;*.gif)|*.jpg;*.jpeg;*.png;*.gif";
                if (ofd.ShowDialog() == DialogResult.OK)
                {
                    txtFotoPath.Text = ofd.FileName;
                    pictureBox1.Image = Image.FromFile(ofd.FileName);
                }
            }
        }

        private void Listele()
        {
            try
            {
                DataTable dt = urunServisi.Listele();
                dataGridView1.DataSource = dt;

                // DataGridViewImageColumn'u kontrol etme ekleme
                if (dataGridView1.Columns["resimColumn"] == null)
                {
                    DataGridViewImageColumn imageColumn = new DataGridViewImageColumn();
                    imageColumn.Name = "resimColumn";
                    imageColumn.HeaderText = "Fotoğraf";
                    imageColumn.ImageLayout = DataGridViewImageCellLayout.Zoom;
                    dataGridView1.Columns.Add(imageColumn);
                }

                foreach (DataGridViewRow row in dataGridView1.Rows)
                {
                    if (row.Cells["resim"] != null && row.Cells["resim"].Value != null)
                    {
                        string resim = row.Cells["resim"].Value.ToString();
                        if (!string.IsNullOrEmpty(resim) && File.Exists(NormalizePath(resim)))
                        {
                            try
                            {
                                using (FileStream fs = new FileStream(NormalizePath(resim), FileMode.Open, FileAccess.Read))
                                {
                                    row.Cells["resimColumn"].Value = Image.FromStream(fs);
                                }
                            }
                            catch (IOException ex)
                            {
                                row.Cells["resimColumn"].Value = null;
                                Console.WriteLine("Fotoğraf yüklenemedi: " + ex.Message);
                            }
                        }
                        else
                        {
                            row.Cells["resimColumn"].Value = null;
                        }
                    }
                    else
                    {
                        row.Cells["resimColumn"].Value = null;
                    }
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show("Veritabanına bağlanırken hata oluştu: " + ex.Message);
            }
        }

        private string SaveImage(string filePath) // fotoğraf ekleme
        {
            string targetDirectory = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "uploads"); // Eklenen fotoğrafın gidiş yolunu ../uploads olarak ata.
            if (!Directory.Exists(targetDirectory))
            {
                Directory.CreateDirectory(targetDirectory);
            }

            string fileName = Path.GetFileName(filePath);
            string targetPath = Path.Combine(targetDirectory, fileName);
            if (!File.Exists(targetPath)) // Aynı dosya mevcut değilse kopyala
            {
                File.Copy(filePath, targetPath, true);
            }

            return NormalizePath(Path.Combine("uploads", fileName));
        }

        private string NormalizePath(string path)
        {
            return path.Replace("\\", "/"); // php için \\ işaretini / işaretine çevirip kaydeder.
        }
    }
}
