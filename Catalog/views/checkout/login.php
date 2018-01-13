
<section>
    <div class="box box-book container">
        <div class ="row">

            <div class="col-md-12">
                <h1 class="page-title">2. Identification</h1>
            </div>

            <div class="col-md-6">
                <div class="box-login">
                    <h3 class="box-title">Connexion</h3>
                    {% form_open id="formLogin" action="formAction" %}
                        {% input_text name="email" model="email" label="Email" placeholder="Identifiant" %}
                        {% input_password name="password" model="password" label="Mot de passe" value="" placeholder="Mot de passe" autocomplete="off" %}
                        {% input_submit name="submit" value="login" formId="formLogin" label="Se connecter" class="btn-primary btn-blue" %}
                    {% form_close %}
                    <p>
                        Mot de passe oublié? <a href="forgotpassword">Cliquez ici</a>
                    </p>
                </div>

            </div>

            <div class="col-md-6">
                <div class="box-register">
                    <h3 class="box-title">Nouveau compte</h3>
                
                    {% form_open id="formSignup" action="formAction" %}
                        {% input_text name="firstname" model="coach.firstname" label="Prénom" %}
                        {% input_text name="lastname" model="coach.lastname" label="Nom" %}
                        {% input_textarea name="address" model="coach.address" label="Adresse" %}
                        {% input_text name="zip_code" model="coach.zip_code" label="Code Postal" %}
                        {% input_text name="city" model="coach.city" label="Ville" %}
                        {% input_text name="phone" model="coach.phone" label="Téléphone" %}
                        {% input_text name="email" model="coach.email" label="Adresse mail" %}
                        {% input_password name="newPassword" model="coach.newPassword" label="Mot de passe" autocomplete="off" %}
                        {% input_password name="newPassword" model="coach.newPassword" label="Confirmation" autocomplete="off" %}                      
                        {% input_submit name="submit" value="save" formId="formSignup" class="btn-primary btn-blue" icon="save" label="Enregistrer" %}
                    {% form_close %}
                </div>
            </div>
        </div>
    </div>
</section>