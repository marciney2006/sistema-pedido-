    </main>

    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-utensils"></i> Lanches Express</h5>
                    <p>Sabor e qualidade em cada pedido. Seu lanche favorito está a apenas um clique!</p>
                </div>
                <div class="col-md-4">
                    <h5>Contato</h5>
                    <p><i class="fas fa-phone"></i> (11) 99999-9999</p>
                    <p><i class="fas fa-envelope"></i> contato@lanchesexpress.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Rua dos Lanches, 123 - São Paulo</p>
                </div>
                <div class="col-md-4">
                    <h5>Horário de Funcionamento</h5>
                    <p><i class="fas fa-clock"></i> Seg-Sex: 11h às 23h</p>
                    <p><i class="fas fa-clock"></i> Sáb-Dom: 12h às 24h</p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2024 Lanches Express. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript Customizado -->
    <script src="<?= SITE_URL ?>/assets/js/app.js"></script>

    <?php if (isset($scripts_adicionais)): ?>
        <?php foreach ($scripts_adicionais as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>