using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace MobilyaSatisOtomasyonu
{
    public partial class FrmLogin : Form
    {
        public FrmLogin()
        {
            InitializeComponent();
        }

        private void button1_Click(object sender, EventArgs e)
        {
            GirisServisi girisServisi = new GirisServisi(); // Classı burada tanımladım.

            string kullaniciAdi = txtKullaniciAdi.Text;
            string parola = txtParola.Text;

            if (string.IsNullOrEmpty(kullaniciAdi) || string.IsNullOrEmpty(parola))
            {
                MessageBox.Show("Lütfen kullanıcı adı ve parola bilgilerinizi giriniz ", "Hata", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            }
            else
            {
                if (girisServisi.KullaniciGiris(kullaniciAdi, parola)) // metot çekme.
                {
                    MessageBox.Show("Giriş başarılı, Ana menüye yönlendiriliyorsunuz... :)", "Login", MessageBoxButtons.OK, MessageBoxIcon.Information);

                    FrmAnamenu frm = new FrmAnamenu();
                    frm.Show();
                    this.Hide();
                }
                else
                {
                    MessageBox.Show("Hatalı kullanıcı adı veya parola ", "Hata ", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                }
            }

            txtKullaniciAdi.Text = "";
            txtParola.Text = "";
        }
    }
}