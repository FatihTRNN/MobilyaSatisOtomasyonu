using System;
using System.Data;
using System.Windows.Forms;
using MySql.Data.MySqlClient;

namespace MobilyaSatisOtomasyonu
{
    public partial class FrmSatisIslemleri : Form
    {
        private MySqlConnection conn;
        private SatisServisi satisServisi;
        private string connectionString = "Server=localhost;Port=3306;Database=mobilyasatis;Uid=root;Pwd=;";

        public FrmSatisIslemleri()
        {
            InitializeComponent();
            conn = new MySqlConnection(connectionString);
            satisServisi = new SatisServisi(conn);
        }

        private void FrmSatisIslemleri_Load(object sender, EventArgs e)
        {
            Listele();
        }

        private void Listele()
        {
            try
            {
                DataTable satislar = satisServisi.ListeleSatislar();
                dataGridView1.DataSource = satislar;

                DataTable urunler = satisServisi.ListeleUrunler();
                cbUrun.DataSource = urunler;
                cbUrun.ValueMember = "urunID";
                cbUrun.DisplayMember = "urunAd";

                DataTable musteriler = satisServisi.ListeleMusteriler();
                cbMusteri.DataSource = musteriler;
                cbMusteri.ValueMember = "musteriID";
                cbMusteri.DisplayMember = "adSoyad";
            }
            catch (Exception ex)
            {
                MessageBox.Show("Veriler yüklenirken hata oluştu: " + ex.Message);
            }
        }

        private void btnHesapla_Click(object sender, EventArgs e)
        {
            if (cbUrun.SelectedValue == null)
            {
                MessageBox.Show("Lütfen bir ürün seçiniz.");
                return;
            }

            int urunID = (int)cbUrun.SelectedValue;
            int adet = (int)numAdet.Value;
            decimal toplamFiyat = satisServisi.HesaplaFiyat(urunID, adet);

            if (toplamFiyat > 0)
            {
                txtBirimFiyat.Text = (toplamFiyat / adet).ToString();
                txtToplamFiyat.Text = toplamFiyat.ToString();
            }
        }

        private void btnSatis_Click(object sender, EventArgs e)
        {
            if (cbUrun.SelectedValue == null || cbMusteri.SelectedValue == null)
            {
                MessageBox.Show("Lütfen bir ürün ve müşteri seçiniz.");
                return;
            }

            if (string.IsNullOrEmpty(txtToplamFiyat.Text))
            {
                MessageBox.Show("Önce hesaplama işlemini yapınız", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            int urunID = (int)cbUrun.SelectedValue;
            int musteriID = (int)cbMusteri.SelectedValue;
            int adet = (int)numAdet.Value;
            decimal toplamFiyat = decimal.Parse(txtToplamFiyat.Text);

            satisServisi.SatisYap(urunID, musteriID, adet, toplamFiyat);
            Listele();
        }
    }
}
