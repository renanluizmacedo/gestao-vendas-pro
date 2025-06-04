    <div class="modal fade" id="parcelamentoModal" tabindex="-1" aria-labelledby="parcelamentoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="parcelamentoModalLabel">Configurar Parcelamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="formParcelamento">
                        <div class="mb-3">
                            <label for="parcelas" class="form-label">Número de Parcelas</label>
                            <select id="parcelas" class="form-control form-select-sm">
                                <option value="1">1x</option>
                                <option value="2">2x</option>
                                <option value="3">3x</option>
                                <option value="4">4x</option>
                                <option value="5">5x</option>
                                <option value="6">6x</option>
                                <option value="7">7x</option>
                                <option value="8">8x</option>
                                <option value="9">9x</option>
                                <option value="10">10x</option>
                                <option value="11">11x</option>
                                <option value="12">12x</option>
                                <option value="13">13x</option>
                                <option value="14">14x</option>
                                <option value="15">15x</option>
                                <option value="16">16x</option>
                                <option value="17">17x</option>
                                <option value="18">18x</option>
                                <option value="19">19x</option>
                                <option value="20">20x</option>
                                <option value="21">21x</option>
                                <option value="22">22x</option>
                                <option value="23">23x</option>
                                <option value="24">24x</option>
                            </select>

                        </div>

                        <p><strong>Valor Total da Venda:</strong> R$ <span id="valorTotalVenda">0,00</span></p>
                        <p hidden>
                            <strong>Valor da Parcela:</strong> R$ <span id="valorParcela">0,00</span>
                        </p>
                        <table class="table table-bordered w-100" id="tabelaVencimentos">
                            <thead>
                                <tr>
                                    <th>Parcela</th>
                                    <th>Data de Vencimento</th>
                                    <th>Valor da Parcela</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>


                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    @if (Route::currentRouteName() === 'sales.create')
                        <button type="button" class="btn btn-primary" id="btnSalvarVenda">Salvar Venda</button>
                    @endif

                </div>
            </div>
        </div>
    </div>
