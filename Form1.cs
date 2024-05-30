using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using System.Net.Http;
using Newtonsoft.Json;

namespace CsharpApi
{
    public partial class Form1 : Form
    {
        private static readonly HttpClient client = new HttpClient();

        public Form1()
        {
            InitializeComponent();
        }


        private async void btnGet_Click(object sender, EventArgs e)
        {
            try
            {
                string url = "http://localhost/api.php?action=get_users";
                HttpResponseMessage response = await client.GetAsync(url);
                response.EnsureSuccessStatusCode();
                string responseBody = await response.Content.ReadAsStringAsync();

                // Populate UserGridView with data
                PopulateDataGridView(UserGridView, responseBody);
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error: " + ex.Message);
            }
        }

        private async void btnPost_Click(object sender, EventArgs e)
        {
            var userData = new { username = txtUsername.Text, email = txtEmail.Text };
            string json = JsonConvert.SerializeObject(userData);
            HttpContent content = new StringContent(json, Encoding.UTF8, "application/json");

            try
            {
                HttpResponseMessage response = await client.PostAsync("http://localhost/api.php?action=create_user", content);
                response.EnsureSuccessStatusCode();
                string responseBody = await response.Content.ReadAsStringAsync();

                // Show success message
                MessageBox.Show("New user successfully added!");
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error: " + ex.Message);
            }
        }


        private async void btnGet1_Click(object sender, EventArgs e)
        {
            try
            {
                HttpResponseMessage response = await client.GetAsync("http://localhost/api.php?action=get_appointments");
                response.EnsureSuccessStatusCode();
                string responseBody = await response.Content.ReadAsStringAsync();

                // Populate AppointmentGrid with data
                PopulateDataGridView(AppointmentGrid, responseBody);
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error: " + ex.Message);
            }
        }

        private async void btnPost1_Click(object sender, EventArgs e)
        {
            int userId;

            // Validate user input
            if (!int.TryParse(txtUserid.Text, out userId))
            {
                MessageBox.Show("Invalid user ID format!");
                return;
            }

            // Get values from DateTimePicker controls
            string appointDate = AppointDate.Value.ToString("yyyy-MM-dd");
            string appointTime = AppointTime.Value.ToString("HH:mm");

            var appointmentData = new
            {
                userID = userId,
                appointmentDate = appointDate,
                appointmentTime = appointTime,
                description = txtDescription.Text
            };
            string json = JsonConvert.SerializeObject(appointmentData);
            HttpContent content = new StringContent(json, Encoding.UTF8, "application/json");

            try
            {
                HttpResponseMessage response = await client.PostAsync("http://localhost/api.php?action=create_appointments", content);
                response.EnsureSuccessStatusCode();
                string responseBody = await response.Content.ReadAsStringAsync();

                // Show success message
                MessageBox.Show("Appointment successfully scheduled!");
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error: " + ex.Message);
            }
        }

        private void PopulateDataGridView(DataGridView dataGridView, string jsonData)
        {
            // Clear existing data
            dataGridView.Rows.Clear();
            dataGridView.Columns.Clear();

            // Deserialize JSON data
            dynamic data = JsonConvert.DeserializeObject(jsonData);
            if (data != null)
            {
                // Add columns dynamically
                foreach (var property in data[0])
                {
                    dataGridView.Columns.Add(property.Name, property.Name);
                }

                // Add rows
                foreach (var item in data)
                {
                    int rowIndex = dataGridView.Rows.Add();
                    foreach (var property in item)
                    {
                        dataGridView.Rows[rowIndex].Cells[property.Name].Value = property.Value.ToString();
                    }
                }
            }
        }

        private void groupBox3_Enter(object sender, EventArgs e)
        {

        }

        private void label4_Click(object sender, EventArgs e)
        {

        }

        private void AppointDate_ValueChanged(object sender, EventArgs e)
        {
            // Event handler code goes here
        }

        private void AppointTime_ValueChanged(object sender, EventArgs e)
        {
            // Event handler code goes here
        }

        private void UserGridView_CellContentClick(object sender, DataGridViewCellEventArgs e)
        {
            // Event handler code goes here
        }

        private void AppointmentGrid_CellContentClick(object sender, DataGridViewCellEventArgs e)
        {
            // Event handler code goes here
        }
    }
}
