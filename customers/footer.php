<?php
/* SIMPLE CONTACT FORM (NO EMAIL SENDING) */
$contact_message = "";
$form_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = isset($_POST["name"]) ? htmlspecialchars(trim($_POST["name"])) : "";
    $email = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : "";
    $message = isset($_POST["message"]) ? htmlspecialchars(trim($_POST["message"])) : "";

    if (!empty($name) && !empty($email) && !empty($message)) {

        // JUST SHOW SUCCESS MESSAGE (NO EMAIL)
        $contact_message = "
        <div style='color:#90EE90; padding:10px; margin:10px 0; border-radius:5px; background:rgba(0,0,0,0.2);'>
            ✓ Message sent successfully!
        </div>";

        $form_success = true;

    } else {
        $contact_message = "
        <div style='color:#FFB6C1; padding:10px; margin:10px 0; border-radius:5px; background:rgba(0,0,0,0.2);'>
            ✗ Please fill all fields.
        </div>";
    }
}
?>

<style>

.footer-container{
    background:#6a0dad;
    color:white;
    padding:30px;
    margin-top:40px;
}

/* MAIN FOOTER */
.footer{
    display:flex;
    justify-content:space-between;
    gap:40px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

/* COLUMNS */
.footer-left,
.footer-right{
    flex:1;
    min-width:250px;
}

/* HEADINGS */
.footer h3{
    color:#ffd700;
    margin-bottom:15px;
    font-size:28px;
    font-weight:bold;
}

.footer h4{
    color:#ffd700;
    margin-bottom:15px;
}

/* TEXT */
.footer p{
    line-height:1.8;
    font-size:15px;
    margin:5px 0;
}

/* FORM */
.contact-form{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.contact-form input,
.contact-form textarea{
    padding:12px;
    border:none;
    border-radius:5px;
    font-size:14px;
    width:100%;
    box-sizing:border-box;
    background:white;
    color:#333;
}

.contact-form textarea{
    resize:vertical;
    min-height:80px;
}

.contact-form button{
    padding:12px;
    background:#ffd700;
    color:#6a0dad;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

.contact-form button:hover{
    background:#fff;
}

/* FOOTER BOTTOM */
.footer-bottom{
    border-top:1px solid rgba(255,255,255,0.2);
    padding-top:15px;
    text-align:center;
    font-size:13px;
}

/* RESPONSIVE */
@media (max-width:768px){
    .footer{ flex-direction:column; }
}

</style>

<div class="footer-container">

    <div class="footer">

        <!-- LEFT -->
        <div class="footer-left">
            <h3>🛍 Stylish Boutique</h3>
            <p>
                <strong style="color:#ffd700;">📍 Address:</strong><br>
                Shop No. 12, Liberty Market<br>
                Gulberg III, Lahore, Pakistan<br><br>

                <strong style="color:#ffd700;">📞 Admin Contact:</strong><br>
                0300-1234567
            </p>
        </div>

        <!-- RIGHT -->
        <div class="footer-right">
            <h4>Contact Us</h4>

            <?php echo $contact_message; ?>

            <form id="contactForm" class="contact-form" method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" placeholder="Your Message" required></textarea>
                <button type="submit">Send Message</button>
            </form>

        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 Stylish Boutique. All rights reserved.</p>
    </div>

</div>

<script>
<?php if ($form_success): ?>
document.getElementById("contactForm").reset();
<?php endif; ?>
</script>