@if (isset($object->surop1_count))
    @php
        $surop1 = explode('-', $object->surop1_count);
        $surop1_number = $surop1[0];
        $surop1_percent = isset($surop1[1]) ? round($surop1[1], 2) : 0;
    @endphp
    <span onclick="chooseSurOp('{{ $object->id }}', 1)" class="suropshow">
        <span>{{ $object->surop1 }}</span>
        <span class="float-left" id="suropshow-{{ $object->id }}-1">{{ $surop1_number }}
            ({{ $surop1_percent }}%)</span>
    </span>
@endif

@if (isset($object->surop2_count))
    @php
        $surop2 = explode('-', $object->surop2_count);
        $surop2_number = $surop2[0];
        $surop2_percent = isset($surop2[1]) ? round($surop2[1], 2) : 0;
    @endphp
    <span onclick="chooseSurOp('{{ $object->id }}', 2)" class="suropshow">
        <span>{{ $object->surop2 }}</span>
        <span class="float-left" id="suropshow-{{ $object->id }}-2">{{ $surop2_number }}
            ({{ $surop2_percent }}%)</span>
    </span>
@endif

@if (isset($object->surop3_count))
    @php
        $surop3 = explode('-', $object->surop3_count);
        $surop3_number = $surop3[0];
        $surop3_percent = isset($surop3[1]) ? round($surop3[1], 2) : 0;
    @endphp
    <span onclick="chooseSurOp('{{ $object->id }}', 3)" class="suropshow">
        <span>{{ $object->surop3 }}</span>
        <span class="float-left" id="suropshow-{{ $object->id }}-3">{{ $surop3_number }}
            ({{ $surop3_percent }}%)</span>
    </span>
@endif

@if (isset($object->surop4_count))
    @php
        $surop4 = explode('-', $object->surop4_count);
        $surop4_number = $surop4[0];
        $surop4_percent = isset($surop4[1]) ? round($surop4[1], 2) : 0;
    @endphp
    <span onclick="chooseSurOp('{{ $object->id }}', 4)" class="suropshow">
        <span>{{ $object->surop4 }}</span>
        <span class="float-left" id="suropshow-{{ $object->id }}-4">{{ $surop4_number }}
            ({{ $surop4_percent }}%)</span>
    </span>
@endif
