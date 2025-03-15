from flask import Flask, render_template, request, redirect, url_for

app = Flask(__name__)

# صفحه اصلی
@app.route('/')
def index():
    return render_template('index.html')

# صفحه درباره ما
@app.route('/about')
def about():
    return render_template('about.html')

# صفحه تماس با ما
@app.route('/contact', methods=['GET', 'POST'])
def contact():
    if request.method == 'POST':
        name = request.form['name']
        email = request.form['email']
        message = request.form['message']
        # ذخیره پیام در دیتابیس یا ارسال ایمیل
        print(f"پیام جدید از {name} ({email}): {message}")
        return redirect(url_for('index'))
    return render_template('contact.html')

if __name__ == '__main__':
    app.run(debug=True)