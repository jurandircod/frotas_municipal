{{-- resources/views/veiculos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Veículos')
@section('page_header', 'Veículos')
@section('page_actions')
  <a href="{{ route('veiculo.store') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm">
    Cadastrar Veículo
  </a>
@endsection

@section('content')
<div class="space-y-4">

  <div class="bg-white rounded-xl p-4 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-sm font-semibold text-gray-700">Lista de Veículos</h3>
        <p class="text-xs text-gray-500">Visualize, edite ou exclua veículos.</p>
      </div>
    </div>

    {{-- Table (desktop) --}}
    <div class="mt-4 hidden md:block">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="text-xs text-gray-500 text-left">
            <tr>
              <th class="px-3 py-2">Id</th>
              <th class="px-3 py-2">Placa</th>
              <th class="px-3 py-2">Marca / Modelo</th>
              <th class="px-3 py-2">Tipo</th>
              <th class="px-3 py-2">KM Atual</th>
              <th class="px-3 py-2">Status</th>
              <th class="px-3 py-2 w-48">Ações</th>
            </tr>
          </thead>
          <tbody>
            @forelse($veiculos as $v)
              <tr class="border-t">
                <td class="px-3 py-3">{{ $v->id }}</td>
                <td class="px-3 py-3 text-gray-800 font-medium">{{ $v->placa }}</td>
                <td class="px-3 py-3">{{ $v->marca }} • {{ $v->modelo }} ({{ $v->ano }})</td>
                <td class="px-3 py-3">{{ $v->tipoVeiculo->nome ?? '-' }}</td>
                <td class="px-3 py-3">{{ number_format($v->km_atual ?? 0, 1, ',', '.') }} km</td>
                <td class="px-3 py-3">
                  @if($v->status === 'ativo')
                    <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700">Ativo</span>
                  @elseif($v->status === 'manutencao')
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-50 text-yellow-700">Manutenção</span>
                  @else
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-50 text-gray-700">{{ ucfirst($v->status) }}</span>
                  @endif
                </td>
                <td class="px-3 py-3">
                  <div class="flex gap-2">
                    <button
                      type="button"
                      class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm border"
                      data-id="{{ $v->id }}"
                      data-placa="{{ $v->placa }}"
                      data-marca="{{ $v->marca }}"
                      data-modelo="{{ $v->modelo }}"
                      data-ano="{{ $v->ano }}"
                      data-cor="{{ $v->cor }}"
                      data-tipo="{{ $v->tipo_veiculo_id }}"
                      data-combustivel="{{ $v->combustivel }}"
                      data-km="{{ $v->km_atual }}"
                      data-status="{{ $v->status }}"
                      data-update-route="{{ route('veiculo.edit', $v->id) }}"
                    >Alterar</button>

                    <form action="{{ route('veiculo.destroy', $v->id) }}" method="POST" onsubmit="return confirmDelete(event, this, '{{ addslashes($v->placa) }}')">
                      @csrf
                      <button type="submit" class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm border">Excluir</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td class="px-3 py-4 text-gray-500" colspan="6">Nenhum veículo cadastrado.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mt-4 md:hidden space-y-3">
      @forelse($veiculos as $v)
        <div class="border rounded-xl p-3">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-sm font-medium text-gray-800">{{ $v->placa }} — {{ $v->marca }} {{ $v->modelo }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ $v->tipoVeiculo->nome ?? '-' }} • {{ $v->ano }}</div>
              <div class="text-xs text-gray-500 mt-1">KM: {{ number_format($v->km_atual ?? 0,1,',','.') }}</div>
            </div>

            <div class="flex flex-col items-end gap-2">
              <button
                type="button"
                class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm"
                data-id="{{ $v->id }}"
                data-placa="{{ $v->placa }}"
                data-marca="{{ $v->marca }}"
                data-modelo="{{ $v->modelo }}"
                data-ano="{{ $v->ano }}"
                data-cor="{{ $v->cor }}"
                data-tipo="{{ $v->tipo_veiculo_id }}"
                data-combustivel="{{ $v->combustivel }}"
                data-km="{{ $v->km_atual }}"
                data-status="{{ $v->status }}"
                data-update-route="{{ route('veiculo.edit', $v->id) }}"
              >Alterar</button>

              <form action="{{ route('veiculo.destroy', $v->id) }}" method="POST" onsubmit="return confirmDelete(event, this, '{{ addslashes($v->placa) }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm">Excluir</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="text-gray-500">Nenhum veículo cadastrado.</div>
      @endforelse
    </div>

    {{-- Pagination --}}
    @if(method_exists($veiculos, 'links'))
      <div class="mt-4">
        {{ $veiculos->links() }}
      </div>
    @endif
  </div>

</div>

<!-- EDIT VEHICLE MODAL -->
<div id="editVehicleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div class="absolute inset-0 bg-black opacity-40" data-close-modal></div>

  <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-800">Alterar Veículo</h3>
      <button type="button" class="text-gray-500" data-close-modal aria-label="Fechar">✕</button>
    </div>

    <form id="editVehicleForm" method="POST" class="px-6 py-6">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <label class="block">
          <span class="text-sm text-gray-700">Placa</span>
          <input type="text" name="placa" id="edit_placa" required class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
        </label>

        <input type="text" name="id" id="edit_id" value="{{ old('id') }}" hidden>

        <label class="block">
          <span class="text-sm text-gray-700">Marca</span>
          <input type="text" name="marca" id="edit_marca" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
        </label>

        <label class="block">
          <span class="text-sm text-gray-700">Modelo</span>
          <input type="text" name="modelo" id="edit_modelo" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
        </label>

        <label class="block">
          <span class="text-sm text-gray-700">Ano</span>
          <input type="number" name="ano" id="edit_ano" min="1900" max="{{ date('Y') + 1 }}" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
        </label>

        <label class="block">
          <span class="text-sm text-gray-700">Cor</span>
          <input type="text" name="cor" id="edit_cor" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
        </label>

        <label class="block">
          <span class="text-sm text-gray-700">Tipo de Veículo</span>
          <select name="tipo_veiculo_id" id="edit_tipo" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
            <option value="">-- selecione --</option>
            @foreach($tipoVeiculos as $t)
              <option value="{{ $t->id }}">{{ $t->nome }}</option>
            @endforeach
          </select>
        </label>

        <label class="block">
          <span class="text-sm text-gray-700">Combustível</span>
          <select name="combustivel" id="edit_combustivel" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
            <option value="">-- selecione --</option>
            <option value="gasolina">Gasolina</option>
            <option value="etanol">Etanol</option>
            <option value="diesel">Diesel</option>
            <option value="flex">Flex</option>
            <option value="eletrico">Elétrico</option>
          </select>
        </label>

        <label class="block">
          <span class="text-sm text-gray-700">KM Atual</span>
          <input type="number" step="0.1" name="km_atual" id="edit_km" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
        </label>

        <label class="block">
          <span class="text-sm text-gray-700">Status</span>
          <select name="status" id="edit_status" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
            <option value="ativo">Ativo</option>
            <option value="manutencao">Em Manutenção</option>
            <option value="inativo">Inativo</option>
          </select>
        </label>
      </div>

      <div class="mt-4 flex justify-end gap-3">
        <button type="button" class="px-4 py-2 rounded-lg border" data-close-modal>Cancelar</button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Salvar alterações</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Confirmação antes de excluir
  function confirmDelete(event, form, placa) {
    event.preventDefault();
    const ok = confirm('Deseja realmente excluir o veículo de placa "' + placa + '"?');
    if (ok) form.submit();
    return false;
  }

  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editVehicleModal');
    const form = document.getElementById('editVehicleForm');
    const openButtons = document.querySelectorAll('.btn-open-edit');
    const closeTriggers = document.querySelectorAll('[data-close-modal]');

    // inputs
    const fPlaca = document.getElementById('edit_placa');
    const fMarca = document.getElementById('edit_marca');
    const fModelo = document.getElementById('edit_modelo');
    const fAno = document.getElementById('edit_ano');
    const fCor = document.getElementById('edit_cor');
    const fTipo = document.getElementById('edit_tipo');
    const fComb = document.getElementById('edit_combustivel');
    const fKm = document.getElementById('edit_km');
    const fStatus = document.getElementById('edit_status');
    const fId = document.getElementById('edit_id');

    openButtons.forEach(btn => {
      btn.addEventListener('click', function () {
        const updateRoute = this.getAttribute('data-update-route');

        // preencher campos
        fPlaca.value = this.getAttribute('data-placa') || '';
        fMarca.value = this.getAttribute('data-marca') || '';
        fModelo.value = this.getAttribute('data-modelo') || '';
        fAno.value = this.getAttribute('data-ano') || '';
        fCor.value = this.getAttribute('data-cor') || '';
        fTipo.value = this.getAttribute('data-tipo') || '';
        fComb.value = this.getAttribute('data-combustivel') || '';
        fKm.value = this.getAttribute('data-km') || '';
        fStatus.value = this.getAttribute('data-status') || 'ativo';
        fId.value = this.getAttribute('data-id') || '';

        // set form action to update route
        form.action = updateRoute;

        // show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => fPlaca.focus(), 150);
      });
    });

    function closeModal() {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    closeTriggers.forEach(t => t.addEventListener('click', closeModal));
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });

    // Optional: client-side validation
    form.addEventListener('submit', function (e) {
      if (!fPlaca.value.trim()) {
        e.preventDefault();
        alert('Informe a placa do veículo.');
        fPlaca.focus();
        return false;
      }
      // deixa o servidor validar o resto
    });
  });
</script>
@endpush