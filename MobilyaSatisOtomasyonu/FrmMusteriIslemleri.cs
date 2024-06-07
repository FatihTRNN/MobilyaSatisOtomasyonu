using System;
using System.Data;
using System.Windows.Forms;
using MySql.Data.MySqlClient;

namespace MobilyaSatisOtomasyonu
{
    public partial class FrmMusteriIslemleri : Form
    {
        private MySqlConnection conn;
        private string connectionString = "Server=localhost;Port=3306;Database=mobilyasatis;Uid=root;Pwd=;";
        private MusteriServisi musteriServisi;

        public FrmMusteriIslemleri()
        {
            InitializeComponent();
            conn = new MySqlConnection(connectionString);
            musteriServisi = new MusteriServisi(conn);
        }

        private void FrmMusteriIslemleri_Load(object sender, EventArgs e)
        {
            listele();
        }

        private void listele()
        {
            dataGridView1.DataSource = musteriServisi.Listele();
            ClearTextBoxes();
        }

        private void ClearTextBoxes()
        {
            txtID.Clear();
            txtAd.Clear();
            txtSoyad.Clear();
            txtAdres.Clear();
            txtTel.Clear();
        }

        private void dataGridView1_CellClick(object sender, DataGridViewCellEventArgs e)
        {
            if (e.RowIndex >= 0)
            {
                DataGridViewRow row = this.dataGridView1.Rows[e.RowIndex];
                txtID.Text = row.Cells["musteriID"].Value.ToString();
                txtAd.Text = row.Cells["ad"].Value.ToString();
                txtSoyad.Text = row.Cells["soyad"].Value.ToString();
                txtAdres.Text = row.Cells["adres"].Value.ToString();
                txtTel.Text = row.Cells["tel"].Value.ToString();
            }
        }

        private void btnListele_Click(object sender, EventArgs e)
        {
            listele();
        }

        private void btnKaydet_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtAd.Text) || string.IsNullOrEmpty(txtSoyad.Text) || string.IsNullOrEmpty(txtAdres.Text) || string.IsNullOrEmpty(txtTel.Text))
            {
                MessageBox.Show("Lütfen tüm alanları giriniz", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            musteriServisi.Kaydet(txtAd.Text, txtSoyad.Text, txtAdres.Text, txtTel.Text);
            Console.WriteLine("Kayıt başarıyla eklendi.");
            listele();
        }

        private void btnSil_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtID.Text))
            {
                MessageBox.Show("Silmek istediğiniz kaydı seçiniz", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            musteriServisi.Sil(int.Parse(txtID.Text));
            Console.WriteLine("Kayıt başarıyla silindi.");
            listele();
        }

        private void btnGuncelle_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtAd.Text) || string.IsNullOrEmpty(txtSoyad.Text) || string.IsNullOrEmpty(txtAdres.Text) || string.IsNullOrEmpty(txtTel.Text))
            {
                MessageBox.Show("Lütfen güncellenecek kaydı seçiniz ve tüm alanları giriniz", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            musteriServisi.Guncelle(int.Parse(txtID.Text), txtAd.Text, txtSoyad.Text, txtAdres.Text, txtTel.Text);
            Console.WriteLine("Kayıt başarıyla güncellendi.");
            listele();
        }

        private void btnBul_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtBul.Text))
            {
                MessageBox.Show("Lütfen aradığınız kelimeyi giriniz", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            dataGridView1.DataSource = musteriServisi.Bul(txtBul.Text);
        }
    }
}